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
    $newName = $path === '' ? $name : "$path.$name";
    if (isFile($tree)) {
        $fileNode = mkfile($newName, getData($tree), getStatus($tree));
        return [ ...$allNodes,  $fileNode ];
    }

    $dirNode = mkdir($newName, [], getStatus($tree));
    $childrenNodes =  array_reduce(
        getChildren($tree),
        fn ($acc, $child) => getAllNodes($child, $newName, $acc),
        []
    );
    return [ ...$allNodes, $dirNode, ...$childrenNodes];
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
        if ($name === '' || $status === '' || $status === 'not changed') {
            return $acc;
        }

        $data = isFile($node) ? getDataAsString($node, true) : '[complex value]';
        if (array_key_exists($name, $acc)) {
            $updatedNodes = [
                "$name" => [
                    'name' => $name,
                    'status' => 'updated',
                    'data' => $data,
                    'prev' => $acc[$name]['data']
                ]
            ];
            $otherdNodes = array_filter($acc, fn ($nodeName) => $nodeName !== $name, ARRAY_FILTER_USE_KEY);
            return [ ...$otherdNodes, ...$updatedNodes ];
        } else {
            $newNodes = [
                "$name" => [
                    'name' => $name, 'status' => $status, 'data' => $data
                ]
            ];
            return [ ...$acc, ...$newNodes ];
        }
    }, []);
}

/**
 * @param array<mixed> $plainItems
 * @return array<string>
 */
function buildOutputLines(array $plainItems): array
{
    return array_map(
        function ($item) {
            switch ($item['status']) {
                case 'added':
                    return "Property '{$item['name']}' was added with value: {$item['data']}";
                case 'removed':
                    return "Property '{$item['name']}' was removed";
                case 'updated':
                    return "Property '{$item['name']}' was updated. From {$item['prev']} to {$item['data']}";
            }
        },
        $plainItems
    );
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
