<?php

namespace Differ\Differ;

use function Differ\Parsers\Json\parse;
use function Differ\OutputFormatter\formatStylish;
use function Differ\TreeComparer\compare;

const FORMAT_STYLISH = 'stylish';

function genDiff(string $firstPath, string $secondPath, string $format = FORMAT_STYLISH): string
{
    $firstData = file_get_contents($firstPath);
    if ($firstData === false) {
        $firstData = '';
    }
    $secondData = file_get_contents($secondPath);
    if ($secondData === false) {
        $secondData = '';
    }
    $diff = compare(parse($firstData), parse($secondData));
    $lines = formatStylish($diff);
    return implode("\n", $lines);
}
