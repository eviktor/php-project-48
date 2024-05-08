<?php

namespace Differ\Formatters;

function getFormatFunction(string $format): mixed
{
    return 'Differ\\Formatters\\' . trim(mb_convert_case($format, MB_CASE_TITLE)) . '\\format';
}

/**
 * @param array<mixed> $diff
 */
function format(array $diff, string $format): string
{
    $formatFunction = getFormatFunction($format);
    return implode("\n", $formatFunction($diff));
}
