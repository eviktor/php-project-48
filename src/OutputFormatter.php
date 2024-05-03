<?php

namespace Differ\OutputFormatter;

use function Php\Immutable\Fs\Trees\trees\getChildren;
use function Php\Immutable\Fs\Trees\trees\getMeta;
use function Php\Immutable\Fs\Trees\trees\getName;
use function Php\Immutable\Fs\Trees\trees\isFile;

function toString(mixed $value): string
{
    $strValue = '';
    if (is_bool($value)) {
        $strValue = ($value ? 'true' : 'false');
    } elseif (is_string($value)) {
        $strValue = $value;
    } elseif (is_array($value)) {
        $strValue = '[ ' . implode(', ', array_map(fn ($v) => toString($v), $value)) . ' ]';
    } elseif (is_null($value)) {
        $strValue = 'null';
    } else {
        $strValue = (string)$value;
    }
    return $strValue;
}

function getStylishSpacing(int $level): string
{
    return str_repeat(' ', max($level * 4 - 2, 0));
}

/**
 * @param array<mixed> $fileNode
 */
function getFileStylishFormattedLine(array $fileNode, int $level): string
{
    $name = getName($fileNode);
    $meta = getMeta($fileNode);
    $status = ' ';
    if (array_key_exists('status', $meta)) {
        $map = [ 'removed' => '-', 'not changed' => ' ', 'added' => '+' ];
        $status = $map[$meta['status']];
    }
    $line = getStylishSpacing($level) . "$status $name";
    if (array_key_exists('data', $meta)) {
        $line .= ': ' . toString($meta['data']);
    }
    return rtrim($line);
}

/**
 * @param array<mixed> $dirNode
 * @return array<mixed>
 */
function getDirStylishFormattedLines(array $dirNode, int $level): array
{
    $spacing = getStylishSpacing($level);
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
        $lines = array_merge($lines, formatStylish($child, $level + 1));
    }
    $lines[] = $level === 0 ? '}' : "$spacing  }";
    return $lines;
}

/**
 * @param array<mixed> $tree
 * @return array<mixed>
 */
function formatStylish(array $tree, int $level = 0): array
{
    $lines = [];

    if (isFile($tree)) {
        $lines[] = getFileStylishFormattedLine($tree, $level);
    } else {
        $lines = array_merge($lines, getDirStylishFormattedLines($tree, $level));
    }

    return $lines;
}
