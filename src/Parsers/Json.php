<?php

namespace Differ\Parsers\Json;

use function Php\Immutable\Fs\Trees\trees\mkdir;
use function Php\Immutable\Fs\Trees\trees\mkfile;

/**
 * @param array<mixed> $jsonData
 * @return array<mixed>
 */
function buildJsonTree(string $name, array $jsonData): array
{
    return mkdir($name, array_map(
        function ($key, $value) {
            return mkfile($key, [ 'data' => $value ]);
        },
        array_keys($jsonData),
        array_values($jsonData)
    ));
}

/**
 * @return array<mixed>|false
 */
function parse(string $content): array|false
{
    $data = json_decode($content, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        if (is_array($data)) {
            return buildJsonTree('', (array)$data);
        }
    }

    return false;
}
