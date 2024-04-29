<?php

namespace Differ\Differ;

use function Differ\Parsers\Json\parse;
use function Differ\OutputFormatter\formatStylish;
use function Differ\TreeComparer\compare;

const FORMAT_STYLISH = 'stylish';

function genDiff(string $firstData, string $secondData, string $format = FORMAT_STYLISH): string
{
    $diff = compare(parse($firstData), parse($secondData));
    $lines = formatStylish($diff);
    return implode("\n", $lines);
}
