<?php

namespace Differ\Parsers\Json;

use function Php\Immutable\Fs\Trees\trees\mkdir;
use function Php\Immutable\Fs\Trees\trees\mkfile;

function isAssocArray(mixed $arr): bool
{
    if (!is_array($arr)) {
        return false;
    }
    if ($arr === []) {
        return true;
    }
    return array_keys($arr) !== range(0, count($arr) - 1);
}

/**
 * @param array<mixed> $jsonData
 * @return array<mixed>
 */
function buildJsonTree(string $name, array $jsonData): array
{
    // ksort($jsonData);
    return mkdir($name, array_map(
        function ($key, $value) {
            if (isAssocArray($value)) {
                return buildJsonTree($key, (array)$value);
            }
            return mkfile($key, [ 'data' => $value ]);
        },
        array_keys($jsonData),
        array_values($jsonData)
    ));
}

/**
 * @return array<mixed>
 */
function parse(string $content, string $rootName = ''): array
{
    $data = json_decode($content, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        if (is_array($data)) {
            return buildJsonTree($rootName, (array)$data);
        // } elseif (is_bool($data)) {
        //     return mkfile(toString($data));
        // } else {
        //     return mkdir($data);
        }
    }

    return mkdir($rootName);
}
