<?php

namespace Differ\Diff\Meta;

/**
 * @return array<mixed>
 */
function mkMeta(string $status, mixed $data): array
{
    return [
        'status' => $status,
        'data' => $data,
    ];
}

/**
 * @param array<mixed> $meta
 */
function getStatus(array $meta): string
{
    return $meta['status'] ?? '';
}

/**
 * @param array<mixed> $meta
 */
function getData(array $meta): mixed
{
    return $meta['data'] ?? null;
}

function toString(mixed $value, bool $useQuotesForStrings = false): string
{
    $strValue = '';
    if (is_array($value)) {
        $strValue = '[ ' . implode(', ', array_map(fn ($v) => toString($v), $value)) . ' ]';
    } elseif (is_null($value)) {
        $strValue = 'null';
    } elseif (is_string($value)) {
        $strValue = ($useQuotesForStrings ? "'$value'" : $value);
    } else {
        $strValue = trim(var_export($value, true), "'");
    }
    return $strValue;
}

/**
 * @param array<mixed> $meta
 */
function getDataAsString(array $meta, bool $useQuotesForStrings = false): string
{
    return toString(getData($meta), $useQuotesForStrings);
}
