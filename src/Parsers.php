<?php

namespace Differ\Parsers;

const VALID_FILE_TYPES = [ 'Json', 'Yml', 'Yaml' ];
const MAP_DUP_TYPES = [ 'Yml' => 'Yaml' ];

function getParseFunction(string $fileType): mixed
{
    $parser = MAP_DUP_TYPES[$fileType] ?? $fileType;
    return "Differ\\Parsers\\$parser\\parse";
}

/**
 * @return array<mixed>|false
 */
function parse(string $content, string $fileType): array|false
{
    $parseFunction = getParseFunction($fileType);
    return $parseFunction($content);
}
