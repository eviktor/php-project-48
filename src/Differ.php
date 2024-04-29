<?php

namespace Differ\Differ;

use function Differ\OutputFormatter\formatStylish;
use function Differ\TreeComparer\compare;

const FORMAT_STYLISH = 'stylish';

function getFileType(string $filePath): string
{
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $format = 'unknown';
    switch ($ext) {
        case 'json':
            $format = 'json';
            break;
        case 'yml':
        case 'yaml':
                $format = 'yaml';
            break;
    }
    return $format;
}

function getParseFunction(string $format): mixed
{
    switch ($format) {
        case 'json':
            return 'Differ\Parsers\Json\parse';
        case 'yaml':
        case 'yml':
            // return 'Differ\Parsers\Yaml\parse';
        default:
            throw new \Exception('Unknown format');
    }
}

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
    $parseFunction = getParseFunction(getFileType($firstPath));
    $diff = compare($parseFunction($firstData), $parseFunction($secondData));
    $lines = formatStylish($diff);
    return implode("\n", $lines);
}
