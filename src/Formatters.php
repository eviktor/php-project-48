<?php

namespace Differ\Formatters;

const VALID_FORMAT_TYPES = [ 'Json', 'Plain', 'Stylish' ];

function getFormatType(string $format): string
{
    $formatType = mb_convert_case($format, MB_CASE_TITLE);
    if (in_array($formatType, VALID_FORMAT_TYPES, true)) {
        return $formatType;
    }
    return 'unknown';
}

function getFormatFunction(string $formatType): mixed
{
    return "Differ\\Formatters\\$formatType\\format";
}

/**
 * @param array<mixed> $diff
 * @return array<mixed>|string Array of formatted lines or error string
*/
function format(array $diff, string $format): array|string
{
    $formatType = getFormatType($format);
    if ($formatType === 'unknown') {
        return "Unknown format $format!";
    }

    $formatFunction = getFormatFunction($formatType);
    return $formatFunction($diff);
}
