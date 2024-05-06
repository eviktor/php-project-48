<?php

namespace Differ\Parsers;

function getParseFunction(string $fileType): mixed
{
    return "Differ\\Parsers\\$fileType\\parse";
}

function parse(string $content, string $fileType): array|false
{
    $parseFunction = getParseFunction($fileType);
    return $parseFunction($content);
}
