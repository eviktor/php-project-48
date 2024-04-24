<?php

/*
function compareTrees(array $firstTree, array $secondTree): array
{
    if (isFile($firstTree) && isFile($secondTree)) {
        $leftData = getMeta($firstTree)['data'];
        $rightData = getMeta($secondTree)['data'];

        if ($leftData === $rightData) {
            return setNodeStatus($firstTree, 'not changed');
        } else {
            $meta = getMeta($secondTree);
            $meta['prev data'] = $leftData;
            return setNodeStatus(mkfile(getName($firstTree), $meta), 'updated');
        }
    } elseif (isDirectory($firstTree) && isDirectory($secondTree)) {
        $leftItems = array_flatten(array_map(fn ($node) => [ getName($node) => $node ], getChildren($firstTree)));
        ksort($leftItems);
        $leftKeys = array_keys($leftItems);

        $rightItems = array_flatten(array_map(fn ($node) => [ getName($node) => $node ], getChildren($secondTree)));
        ksort($rightItems);
        $rightKeys = array_keys($rightItems);

        $removedKeys = array_diff($leftKeys, $rightKeys);
        //$sameKeys = array_intersect($leftKeys, $rightKeys);
        $addedKeys = array_diff($rightKeys, $leftKeys);
        $allKeys = array_unique(array_merge($leftKeys, $rightKeys));

        $newChildren = array_map(function ($key) use ($removedKeys, $addedKeys, $leftItems, $rightItems) {
            if (in_array($key, $removedKeys)) {
                return setNodeStatus($leftItems[$key], 'removed');
            } elseif (in_array($key, $removedKeys)) {
                return setNodeStatus($rughtItems[$key], 'added');
            } else {
                $leftItem = $leftItems[$key];
                $rightItem = $rightItems[$key];
                return compareTrees($leftItem, $rightItem);
            }
        }, $allKeys);
    } else {
        return setNodeStatus($firstTree, 'removed');
    }

    return mkdir('');
}
*/
