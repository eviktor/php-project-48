<?php

namespace Differ\Diff\Tree;

// Modified hexlet tree

/**
 * Make directory node
 * @param string $name
 * @param array<mixed> $children
 * @param ?string $status
 * @return array<mixed>
 */
function mkdir(string $name, array $children = [], ?string $status = null): array
{
    $node = [
        "name" => $name,
        "children" => $children,
        "type" => "directory",
    ];
    if (!is_null($status)) {
        $node["status"] = $status;
    }
    return $node;
}

/**
 * Make file node
 * @param string $name
 * @param mixed $data
 * @param ?string $status
 * @return array<mixed>
 */
function mkfile(string $name, mixed $data, ?string $status = null): array
{
    $node = [
        "name" => $name,
        "data" => $data,
        "type" => "file",
    ];
    if (!is_null($status)) {
        $node["status"] = $status;
    }
    return $node;
}


/**
 * Return children
 * @param array<mixed> $node
 * @return array<mixed>
 */
function getChildren($node)
{
    return $node['children'];
}

/**
 * Return status
 * @param array<mixed> $node
 * @return ?string
 */
function getStatus($node): ?string
{
    return $node['status'] ?? null;
}

/**
 * Return status
 * @param array<mixed> $node
 * @return mixed
 */
function getData($node): mixed
{
    return $node['data'];
}

/**
 * Return name
 * @param array<mixed> $node
 * @return string
 */
function getName($node): string
{
    return $node['name'];
}

/**
 * Test directory
 * @param array<mixed> $node
 * @return boolean
 */
function isFile($node): bool
{
    return $node['type'] == 'file';
}

/**
 * Test file
 * @param array<mixed> $node
 * @return boolean
 */
function isDirectory($node): bool
{
    return $node['type'] == 'directory';
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
 * @param array<mixed> $node
 */
function getDataAsString(array $node, bool $useQuotesForStrings = false): string
{
    return toString(getData($node), $useQuotesForStrings);
}
