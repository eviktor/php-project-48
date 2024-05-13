<?php

namespace Differ\Differ;

use function Differ\Diff\Builder\compare;
use function Differ\Formatters\format;
use function Differ\Parsers\parse;

function genDiff(string $firstPath, string $secondPath, string $format = 'stylish'): string
{
    $data1 = parse($firstPath);
    if (!is_array($data1)) {
        return $data1;
    }
    $data2 = parse($secondPath);
    if (!is_array($data2)) {
        return $data2;
    }

    $diff = compare($data1, $data2);

    $formattedLines = format($diff, $format);
    if (!is_array($formattedLines)) {
        return $formattedLines;
    }
    return implode(PHP_EOL, $formattedLines);
}
