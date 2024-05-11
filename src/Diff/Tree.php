<?php

namespace Differ\Diff\Tree;

// Modified hexlet tree

/**
 * Make directory node
 * @param string $name
 * @param array<mixed> $children
 * @param string $status
 * @return array<mixed>
 */
function mkdir(string $name, array $children = [], string $status = ''): array
{
    return [
        "name" => $name,
        "children" => $children,
        "status" => $status,
        "type" => "directory"
    ];
}

/**
 * Make file node
 * @param string $name
 * @param mixed $data
 * @param string $status
 * @return array<mixed>
 */
function mkfile(string $name, mixed $data, string $status = ''): array
{
    return [
        "name" => $name,
        "data" => $data,
        "status" => $status,
        "type" => "file"
    ];
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
 * @return string
 */
function getStatus($node): string
{
    return $node['status'];
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
    if (is_array($value)) {
        $data = implode(', ', array_map(fn ($v) => toString($v), $value));
        return "[ $data ]";
    } elseif (is_null($value)) {
        return 'null';
    } elseif (is_string($value)) {
        return ($useQuotesForStrings ? "'$value'" : $value);
    }
    return trim(var_export($value, true), "'");
}

/**
 * @param array<mixed> $node
 */
function getDataAsString(array $node, bool $useQuotesForStrings = false): string
{
    return toString(getData($node), $useQuotesForStrings);
}
