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
    return str_repeat(' ', $level * 2);
}

/**
 * @param array<mixed> $fileNode
 */
function getFileStylishFormattedLine(array $fileNode, int $level): string
{
    $name = getName($fileNode);
    $meta = getMeta($fileNode);
    $status = '';
    if (array_key_exists('status', $meta)) {
        $map = [ 'removed' => '-', 'not changed' => ' ', 'added' => '+' ];
        $status = $map[$meta['status']];
    }
    $line = getStylishSpacing($level) . "$status $name";
    if (array_key_exists('data', $meta)) {
        $line .= ': ' . toString($meta['data']);
    }
    return $line;
}

/**
 * @param array<mixed> $dirNode
 * @return array<mixed>
 */
function getDirStylishFormattedLines(array $dirNode, int $level): array
{
    $spacing = getStylishSpacing($level);
    $lines = [ "$spacing{" ];
    $name = getName($dirNode);
    if (!empty($name)) {
        $lines[] = "$spacing$name:";
    }
    $children = getChildren($dirNode);
    foreach ($children as $child) {
        $lines = array_merge($lines, formatStylish($child, $level + 1));
    }
    $lines[] = "$spacing}";
    return $lines;
}

/**
 * @param array<mixed> $tree
 * @return array<mixed>
 */
function formatStylish(array $tree, int $level = 0): array
{
    $lines = [];
    $spacing = str_repeat(' ', $level * 2);

    if (isFile($tree)) {
        $lines[] = getFileStylishFormattedLine($tree, $level);
    } else {
        $lines = array_merge($lines, getDirStylishFormattedLines($tree, $level));
    }

    return $lines;
}
