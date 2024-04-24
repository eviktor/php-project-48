<?php

namespace Differ\Differ;

use function Php\Immutable\Fs\Trees\trees\mkdir;
use function Php\Immutable\Fs\Trees\trees\mkfile;
use function Php\Immutable\Fs\Trees\trees\getChildren;
use function Php\Immutable\Fs\Trees\trees\getMeta;
use function Php\Immutable\Fs\Trees\trees\getName;
use function Php\Immutable\Fs\Trees\trees\isDirectory;
use function Php\Immutable\Fs\Trees\trees\isFile;
use function Differ\ImportJson\importJson;
use function Differ\OutputFormatter\formatStylish;

const FORMAT_STYLISH = 'stylish';

/**
 * @param array<mixed> $node
 * @return array<mixed>
 */
function setNodeStatus(array $node, string $status): array
{
    $name = getName($node);
    $meta = getMeta($node);
    if (isFile($node)) {
        $meta['status'] = $status;
        return mkfile($name, $meta);
    } else {
        $children = getChildren($node);
        $newChildren = array_map(fn ($child) => setNodeStatus($child, $status), $children);
        return mkdir($name, $newChildren, $meta);
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
    }
    return false;
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
            if (in_array($key, $removedKeys)) {
                $acc[] = setNodeStatus($leftItems[$key], 'removed');
            } elseif (in_array($key, $addedKeys)) {
                $acc[] = setNodeStatus($rightItems[$key], 'added');
            } elseif (isEqualNodeData($leftItems[$key], $rightItems[$key])) {
                $acc[] = setNodeStatus($leftItems[$key], 'not changed');
            } else {
                $acc[] = setNodeStatus($leftItems[$key], 'removed');
                $acc[] = setNodeStatus($rightItems[$key], 'added');
            }
            return $acc;
        },
        []
    );
    usort($newChildren, fn ($a, $b) => getName($a) <=> getName($b));

    return mkdir(getName($firstTree), $newChildren, getMeta($firstTree));
}

function genDiff(string $firstData, string $secondData, string $format = FORMAT_STYLISH): string
{
    $diff = compare(importJson($firstData), importJson($secondData));
    $lines = formatStylish($diff);
    return implode("\n", $lines);
}
