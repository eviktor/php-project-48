<?php

namespace Differ\Formatters;

const VALID_FORMAT_TYPES = [ 'Json', 'Plain', 'Stylish' ];

function getFormatFunction(string $formatType): mixed
{
    return "Differ\\Formatters\\$formatType\\format";
}

/**
 * @param array<mixed> $diff
 */
function format(array $diff, string $formatType): string
{
    $formatFunction = getFormatFunction($formatType);
    return implode("\n", $formatFunction($diff));
}
