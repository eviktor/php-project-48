<?php

namespace Differ\DiffMeta;

function mkMeta(string $status, mixed $data): array
{
    return [
        'status' => $status,
        'data' => $data,
    ];
}

function getStatus(array $meta): string
{
    return $meta['status'] ?? '';
}

function getData(array $meta): mixed
{
    return $meta['data'] ?? null;
}
