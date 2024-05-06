<?php

namespace Differ\Differ;

use function Differ\Diff\Builder\compare;
use function Differ\Formatters\format;
use function Differ\Parsers\parse;

function getFileType(string $filePath): string
{
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $format = 'unknown';
    switch ($ext) {
        case 'json':
            $format = 'Json';
            break;
        case 'yml':
        case 'yaml':
            $format = 'Yaml';
            break;
    }
    return $format;
}

/**
 * @return array<mixed>|string
 */
function parseFile(string $filePath): array|string
{
    $data = file_get_contents($filePath);
    if ($data === false) {
        return "Error reading file $filePath!";
    }

    $fileType = getFileType($filePath);
    if ($fileType === 'unknown') {
        return "Unknown file type $fileType!";
    }

    $parsedData = parse($data, $fileType);
    if ($parsedData === false) {
        return "Error parsing file $filePath!";
    }
    return $parsedData;
}

function genDiff(string $firstPath, string $secondPath, string $format = 'stylish'): string
{
    $data1 = parseFile($firstPath);
    if (!is_array($data1)) {
        return $data1;
    }
    $data2 = parseFile($secondPath);
    if (!is_array($data2)) {
        return $data2;
    }

    $diff = compare($data1, $data2);
    return format($diff, $format);
}
