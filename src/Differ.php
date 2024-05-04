<?php

namespace Differ\Differ;

use function Differ\TreeComparer\compare;

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

function getParseFunction(string $fileType): mixed
{
    return "Differ\\Parsers\\$fileType\\parse";
}

function getFormatFunction(string $format): mixed
{
    $format = trim(mb_convert_case($format, MB_CASE_TITLE));
    return "Differ\\Formatters\\$format\\format";
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

    $parseFunction = getParseFunction($fileType);
    $parsedData = $parseFunction($data);
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
    $formatFunction = getFormatFunction($format);
    return implode("\n", $formatFunction($diff));
}
