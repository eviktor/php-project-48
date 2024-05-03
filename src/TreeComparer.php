<?php

namespace Differ\TreeComparer;

use function Php\Immutable\Fs\Trees\trees\mkdir;
use function Php\Immutable\Fs\Trees\trees\mkfile;
use function Php\Immutable\Fs\Trees\trees\getChildren;
use function Php\Immutable\Fs\Trees\trees\getMeta;
use function Php\Immutable\Fs\Trees\trees\getName;
use function Php\Immutable\Fs\Trees\trees\isDirectory;
use function Php\Immutable\Fs\Trees\trees\isFile;

/**
 * @param array<mixed> $node
 * @return array<mixed>
 */
function setNodeStatus(array $node, string $status): array
{
    $name = getName($node);
    $meta = getMeta($node);
    $meta['status'] = $status;
    if (isFile($node)) {
        return mkfile($name, $meta);
    } else {
        $children = getChildren($node);
        // $newChildren = array_map(fn ($child) => setNodeStatus($child, $status), $children);
        // return mkdir($name, $newChildren, $meta);
        return mkdir($name, $children, $meta);
    }
}

/**
 * @param array<mixed> $firstNode
 * @param array<mixed> $secondNode
*/
function isEqualNodeData(array $firstNode, array $secondNode): bool
{
    if (isFile($firstNode) && isFile($secondNode)) {
        return getMeta($firstNode)['data'] === getMeta($secondNode)['data'];
    } elseif (isDirectory($firstNode) && isDirectory($secondNode)) {
        return getName($firstNode) === getName($secondNode);
    }
    return false;
}

/**
 * @param array<mixed> $leftItem
 * @param array<mixed> $rightItem
 * @return array<mixed>
 */
function compareNodeItems(array $leftItem, array $rightItem, bool $isRemoved, bool $isAdded): array
{
    $res = [];
    if ($isRemoved) {
        $res[] = setNodeStatus($leftItem, 'removed');
    } elseif ($isAdded) {
        $res[] = setNodeStatus($rightItem, 'added');
    } elseif (isEqualNodeData($leftItem, $rightItem)) {
        if (isFile($leftItem) && isFile($rightItem)) {
            $res[] = setNodeStatus($leftItem, 'not changed');
        } else {
            $res[] = compare($leftItem, $rightItem);
        }
    } else {
        $res[] = setNodeStatus($leftItem, 'removed');
        $res[] = setNodeStatus($rightItem, 'added');
    }
    return $res;
}

/**
 * @param array<mixed> $firstTree
 * @param array<mixed> $secondTree
 * @return array<mixed>
 */
function compare(array $firstTree, array $secondTree): array
{
    if (!isDirectory($firstTree) || !isDirectory($secondTree)) {
        return mkdir('');
    }

    $leftItems = array_merge(...array_map(fn ($node) => [ getName($node) => $node ], getChildren($firstTree)));
    $rightItems = array_merge(...array_map(fn ($node) => [ getName($node) => $node ], getChildren($secondTree)));

    $removedKeys = array_diff(array_keys($leftItems), array_keys($rightItems));
    $addedKeys = array_diff(array_keys($rightItems), array_keys($leftItems));
    $allKeys = array_unique(array_merge(array_keys($leftItems), array_keys($rightItems)));

    $newChildren = array_reduce(
        $allKeys,
        function ($acc, $key) use ($removedKeys, $addedKeys, $leftItems, $rightItems) {
            return array_merge($acc, compareNodeItems(
                $leftItems[$key] ?? [],
                $rightItems[$key] ?? [],
                in_array($key, $removedKeys),
                in_array($key, $addedKeys)
            ));
        },
        []
    );
    usort($newChildren, fn ($a, $b) => getName($a) <=> getName($b));

    return mkdir(getName($firstTree), $newChildren, getMeta($firstTree));
}
