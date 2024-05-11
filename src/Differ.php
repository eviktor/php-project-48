<?php

namespace Differ\Differ;

use function Differ\Diff\Builder\compare;
use function Differ\Formatters\format;
use function Differ\Parsers\parse;

use const Differ\Parsers\VALID_FILE_TYPES;
use const Differ\Formatters\VALID_FORMAT_TYPES;

function getFileType(string $filePath): string
{
    $ext = mb_convert_case(pathinfo($filePath, PATHINFO_EXTENSION), MB_CASE_TITLE);
    if (in_array($ext, VALID_FILE_TYPES, true)) {
        return $ext;
    }
    return 'unknown';
}

function getFormatType(string $format): string
{
    $formatType = mb_convert_case($format, MB_CASE_TITLE);
    if (in_array($formatType, VALID_FORMAT_TYPES, true)) {
        return $formatType;
    }
    return 'unknown';
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
        return "Unknown file type of $filePath!";
    }

    $parsedData = parse($data, $fileType);
    if ($parsedData === false) {
        return "Error parsing file $filePath!";
    }
    return $parsedData;
}

function genDiff(string $firstPath, string $secondPath, string $format = 'stylish'): string
{
    $formatType = getFormatType($format);
    if ($formatType === 'unknown') {
        return "Unknow format $format!";
    }

    $data1 = parseFile($firstPath);
    if (!is_array($data1)) {
        return $data1;
    }
    $data2 = parseFile($secondPath);
    if (!is_array($data2)) {
        return $data2;
    }

    $diff = compare($data1, $data2);
    return format($diff, $formatType);
}
