<?php

namespace Differ\Formatters\Plain;

use function Differ\Diff\Tree\mkdir;
use function Differ\Diff\Tree\mkfile;
use function Differ\Diff\Tree\getChildren;
use function Differ\Diff\Tree\getName;
use function Differ\Diff\Tree\getStatus;
use function Differ\Diff\Tree\getData;
use function Differ\Diff\Tree\getDataAsString;
use function Differ\Diff\Tree\isFile;

/**
 * @param array<mixed> $tree
 * @param array<mixed> $allNodes
 * @return array<mixed>
 */
function getAllNodes(array $tree, string $path = '', array $allNodes = []): array
{
    $name = getName($tree);
    $newName = empty($path) ? $name : "$path.$name";
    if (isFile($tree)) {
        $allNodes[] = mkfile($newName, getData($tree), getStatus($tree));
    } else {
        $allNodes[] = mkdir($newName, [], getStatus($tree));
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
        $status = getStatus($node);
        if (empty($name) || empty($status) || $status === 'not changed') {
            return $acc;
        }

        $data = isFile($node) ? getDataAsString($node, true) : '[complex value]';

        if (array_key_exists($name, $acc)) {
            $acc[$name] = [ 'name' => $name, 'status' => 'updated', 'data' => $data, 'prev' => $acc[$name]['data']];
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
function format(array $tree): array
{
    $nodes = getAllNodes($tree);
    $plainItems = getPlainItems($nodes);
    $lines = buildOutputLines($plainItems);
    return $lines;
}
