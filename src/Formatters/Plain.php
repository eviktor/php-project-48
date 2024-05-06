<?php

namespace Differ\Formatters\Plain;

use function Php\Immutable\Fs\Trees\trees\mkdir;
use function Php\Immutable\Fs\Trees\trees\mkfile;
use function Php\Immutable\Fs\Trees\trees\getChildren;
use function Php\Immutable\Fs\Trees\trees\getMeta;
use function Php\Immutable\Fs\Trees\trees\getName;
use function Php\Immutable\Fs\Trees\trees\isFile;
use function Differ\Diff\Meta\mkMeta;
use function Differ\Diff\Meta\getStatus;
use function Differ\Diff\Meta\getData;
use function Differ\Diff\Meta\getDataAsString;

/**
 * @param array<mixed> $tree
 * @param array<mixed> $allNodes
 * @return array<mixed>
 */
function getAllNodes(array $tree, string $path = '', array $allNodes = []): array
{
    $name = getName($tree);
    $meta = getMeta($tree);

    $newName = empty($path) ? $name : "$path.$name";
    $newMeta = mkMeta(getStatus($meta), getData($meta));
    if (isFile($tree)) {
        $allNodes[] = mkFile($newName, $newMeta);
    } else {
        $allNodes[] = mkDir($newName, [], $newMeta);
        $children = getChildren($tree);
        foreach ($children as $child) {
            $allNodes = getAllNodes($child, $newName, $allNodes);
        }
    }

    return $allNodes;
}

/**
 * @param array<mixed> $allNodes
 * @return array<mixed>
 */
function getPlainItems(array $allNodes): array
{
    return array_reduce($allNodes, function ($acc, $node) {
        $name = getName($node);
        $meta = getMeta($node);
        $status = getStatus($meta);
        if (empty($name) || empty($status) || $status === 'not changed') {
            return $acc;
        }

        $data = isFile($node) ? getDataAsString($meta, true) : '[complex value]';

        if (array_key_exists($name, $acc)) {
            $acc[$name]['status'] = 'updated';
            $acc[$name]['prev'] = $acc[$name]['data'];
            $acc[$name]['data'] = $data;
        } else {
            $acc[$name] = [ 'name' => $name, 'status' => $status, 'data' => $data ];
        }
        return $acc;
    }, []);
}

/**
 * @param array<mixed> $plainItems
 * @return array<string>
 */
function buildOutputLines(array $plainItems): array
{
    $lines = [];
    foreach ($plainItems as $item) {
        switch ($item['status']) {
            case 'added':
                $lines[] = "Property '{$item['name']}' was added with value: {$item['data']}";
                break;
            case 'removed':
                $lines[] = "Property '{$item['name']}' was removed";
                break;
            case 'updated':
                $lines[] = "Property '{$item['name']}' was updated. From {$item['prev']} to {$item['data']}";
                break;
        }
    }
    return $lines;
}

/**
 * @param array<mixed> $tree
 * @return array<mixed>
 */
function format(array $tree, int $level = 0): array
{
    $nodes = getAllNodes($tree);
    $plainItems = getPlainItems($nodes);
    $lines = buildOutputLines($plainItems);
    return $lines;
}
