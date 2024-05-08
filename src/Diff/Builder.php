<?php

namespace Differ\Diff\Builder;

use function Functional\sort as fsort;
use function Differ\Diff\Tree\mkdir;
use function Differ\Diff\Tree\mkfile;
use function Differ\Diff\Tree\getChildren;
use function Differ\Diff\Tree\getName;
use function Differ\Diff\Tree\getStatus;
use function Differ\Diff\Tree\getData;
use function Differ\Diff\Tree\isDirectory;
use function Differ\Diff\Tree\isFile;

/**
 * @param array<mixed> $node
 * @return array<mixed>
 */
function setNodeStatus(array $node, string $status): array
{
    if (isFile($node)) {
        return mkfile(getName($node), getData($node), $status);
    } else {
        return mkdir(getName($node), getChildren($node), $status);
    }
}

/**
 * @param array<mixed> $firstNode
 * @param array<mixed> $secondNode
*/
function isEqualNodeData(array $firstNode, array $secondNode): bool
{
    if (isFile($firstNode) && isFile($secondNode)) {
        return getData($firstNode) === getData($secondNode);
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
    if ($isRemoved) {
        return [ setNodeStatus($leftItem, 'removed') ];
    }
    if ($isAdded) {
        return [ setNodeStatus($rightItem, 'added') ];
    }
    if (!isEqualNodeData($leftItem, $rightItem)) {
        return [
            setNodeStatus($leftItem, 'removed'),
            setNodeStatus($rightItem, 'added')
        ];
    }
    return handleEqualNodeData($leftItem, $rightItem);
}

/**
 * @param array<mixed> $leftItem
 * @param array<mixed> $rightItem
 * @return array<mixed>
 */
function handleEqualNodeData(array $leftItem, array $rightItem): array
{
    if (isFile($leftItem) && isFile($rightItem)) {
        return [ setNodeStatus($leftItem, 'not changed') ];
    } else {
        return [ compare($leftItem, $rightItem) ];
    }
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
                in_array($key, $removedKeys, true),
                in_array($key, $addedKeys, true)
            ));
        },
        []
    );
    $sortedChildren = fsort($newChildren, fn ($a, $b) => getName($a) <=> getName($b));
    return mkdir(getName($firstTree), $sortedChildren, getStatus($firstTree));
}
