<?php

namespace Differ\Formatters\Stylish;

use function Differ\Diff\Tree\getChildren;
use function Differ\Diff\Tree\getName;
use function Differ\Diff\Tree\getStatus;
use function Differ\Diff\Tree\getData;
use function Differ\Diff\Tree\getDataAsString;
use function Differ\Diff\Tree\isFile;

function getSpacing(int $level): string
{
    return str_repeat(' ', max($level * 4 - 2, 0));
}

function getStatusSymbol(string $status): string
{
    $map = [ 'removed' => '-', 'not changed' => ' ', 'added' => '+', '' => ' ' ];
    return $map[$status];
}

/**
 * @param array<mixed> $fileNode
 * @return array<mixed>
 */
function buildFileLines(array $fileNode, int $level): array
{
    $spacing = getSpacing($level);
    $name = getName($fileNode);
    $statusSymbol = getStatusSymbol(getStatus($fileNode));
    $strData = getDataAsString($fileNode);
    return [ "$spacing$statusSymbol $name: $strData" ];
}

/**
 * @param array<mixed> $dirNode
 * @return array<mixed>
 */
function buildDirLines(array $dirNode, int $level): array
{
    $spacing = getSpacing($level);
    $name = getName($dirNode);
    $statusSymbol = getStatusSymbol(getStatus($dirNode));
    $beginLine = $level === 0 ? '{' : "$spacing$statusSymbol $name: {";

    $childrenLines = array_reduce(
        getChildren($dirNode),
        fn ($acc, $child) => array_merge($acc, buildOutputLines($child, $level + 1)),
        []
    );

    $endLine = $level === 0 ? '}' : "$spacing  }";
    return [ $beginLine, ...$childrenLines, $endLine ];
}

/**
 * @param array<mixed> $tree
 * @return array<mixed>
 */
function buildOutputLines(array $tree, int $level): array
{
    return isFile($tree) ? buildFileLines($tree, $level) : buildDirLines($tree, $level);
}

/**
 * @param array<mixed> $tree
 * @return array<mixed>
 */
function format(array $tree): array
{
    return buildOutputLines($tree, 0);
}
