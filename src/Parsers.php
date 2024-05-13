<?php

namespace Differ\Parsers;

const MAP_EXT_TO_PARSER = [ 'Json' => 'Json', 'Yml' => 'Yaml', 'Yaml' => 'Yaml' ];

function getParserName(string $filePath): string
{
    $ext = mb_convert_case(pathinfo($filePath, PATHINFO_EXTENSION), MB_CASE_TITLE);
    return MAP_EXT_TO_PARSER[$ext] ?? 'unknown';
}

function getParseFunction(string $parserName): mixed
{
    return "Differ\\Parsers\\$parserName\\parse";
}

/**
 * @return array<mixed>|string Parsed data or error string
 */
function parse(string $filePath): array|string
{
    $data = file_get_contents($filePath);
    if ($data === false) {
        return "Error reading file $filePath!";
    }

    $parserName = getParserName($filePath);
    if ($parserName === 'unknown') {
        return "Unknown file type of $filePath!";
    }

    $parseFunction = getParseFunction($parserName);
    $res = $parseFunction($data);
    if ($res === false) {
        return "Error parsing file $filePath!";
    }
    return $res;
}
