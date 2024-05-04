<?php

namespace Differ\Formatters\Stylish;

use function Php\Immutable\Fs\Trees\trees\getChildren;
use function Php\Immutable\Fs\Trees\trees\getMeta;
use function Php\Immutable\Fs\Trees\trees\getName;
use function Php\Immutable\Fs\Trees\trees\isFile;

function toString(mixed $value): string
{
    $strValue = '';
    if (is_array($value)) {
        $strValue = '[ ' . implode(', ', array_map(fn ($v) => toString($v), $value)) . ' ]';
    } elseif (is_null($value)) {
        $strValue = 'null';
    } else {
        $strValue = trim(var_export($value, true), "'");
    }
    return $strValue;
}

function getSpacing(int $level): string
{
    return str_repeat(' ', max($level * 4 - 2, 0));
}

/**
 * @param array<mixed> $fileNode
 */
function buildFileLine(array $fileNode, int $level): string
{
    $name = getName($fileNode);
    $meta = getMeta($fileNode);
    $status = ' ';
    if (array_key_exists('status', $meta)) {
        $map = [ 'removed' => '-', 'not changed' => ' ', 'added' => '+' ];
        $status = $map[$meta['status']];
    }
    $line = getSpacing($level) . "$status $name";
    if (array_key_exists('data', $meta)) {
        $line .= ': ' . toString($meta['data']);
    }
    return rtrim($line);
}

/**
 * @param array<mixed> $dirNode
 * @return array<mixed>
 */
function buildDirLines(array $dirNode, int $level): array
{
    $spacing = getSpacing($level);
    $name = getName($dirNode);
    $meta = getMeta($dirNode);
    $lines = [];
    $status = ' ';
    if (array_key_exists('status', $meta)) {
        $map = [ 'removed' => '-', 'not changed' => ' ', 'added' => '+' ];
        $status = $map[$meta['status']];
    }
    $lines[] = $level === 0 ? '{' : "$spacing$status $name: {";
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
