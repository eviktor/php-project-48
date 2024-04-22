<?php

namespace Differ\OutputFormatter;

use function Php\Immutable\Fs\Trees\trees\getChildren;
use function Php\Immutable\Fs\Trees\trees\getMeta;
use function Php\Immutable\Fs\Trees\trees\getName;
use function Php\Immutable\Fs\Trees\trees\isFile;

function toString(mixed $value): string
{
    if (is_bool($value)) {
        return ($value ? 'true' : 'false');
    } elseif (is_string($value)) {
        //return '"' . $value . '"';
        return $value;
    } elseif (is_array($value)) {
        return '[ ' . implode(', ', array_map(fn ($v) => toString($v), $value)) . ' ]';
    } elseif (is_null($value)) {
        return 'null';
    }
    return (string)$value;
}

/**
 * @param array<mixed> $tree
 * @return array<mixed>
 */
function formatStylish(array $tree, int $level = 0): array
{
    $lines = [];

    $spacing = str_repeat(' ', $level * 2);
    $name = getName($tree);

    if (isFile($tree)) {
        $meta = getMeta($tree);
        $status = '';
        if (array_key_exists('status', $meta)) {
            $map = [ 'removed' => '-', 'not changed' => ' ', 'added' => '+' ];
            $status = $map[$meta['status']];
        }
        $line = "$spacing$status $name";
        if (array_key_exists('data', $meta)) {
            $line .= ': ' . toString($meta['data']);
        }
        $lines[] = $line;
    } else {
        $lines[] = "$spacing" . (empty($name) ? '' : "$name:");
        $lines[] = "$spacing{";
        $children = getChildren($tree);
        foreach ($children as $child) {
            $lines = array_merge($lines, formatStylish($child, $level + 1));
        }
        $lines[] = "$spacing}";
    }

    return $lines;
}
