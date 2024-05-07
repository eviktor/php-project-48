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

function getStatusSymbol(?string $status): string
{
    $map = [ 'removed' => '-', 'not changed' => ' ', 'added' => '+', '' => ' ', null => ' ' ];
    return $map[$status];
}

/**
 * @param array<mixed> $fileNode
 */
function buildFileLine(array $fileNode, int $level): string
{
    $spacing = getSpacing($level);
    $name = getName($fileNode);
    $statusSymbol = getStatusSymbol(getStatus($fileNode));
    $strData = getDataAsString($fileNode);
    $line = "$spacing$statusSymbol $name: $strData";
    return rtrim($line);
}

/**
 * @param array<mixed> $dirNode
 * @return array<mixed>
 */
function buildDirLines(array $dirNode, int $level): array
{
    $lines = [];

    $spacing = getSpacing($level);
    $name = getName($dirNode);
    $statusSymbol = getStatusSymbol(getStatus($dirNode));
    $lines[] = $level === 0 ? '{' : "$spacing$statusSymbol $name: {";

    $children = getChildren($dirNode);
    foreach ($children as $child) {
        $lines = array_merge($lines, format($child, $level + 1));
    }

    $lines[] = $level === 0 ? '}' : "$spacing  }";

    return $lines;
}

/**
 * @param array<mixed> $tree
 * @return array<mixed>
 */
function format(array $tree, int $level = 0): array
{
    $lines = [];

    if (isFile($tree)) {
        $lines[] = buildFileLine($tree, $level);
    } else {
        $lines = array_merge($lines, buildDirLines($tree, $level));
    }

    return $lines;
}
