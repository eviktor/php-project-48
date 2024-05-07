<?php

namespace Differ\Formatters\Json;

/**
 * @param array<mixed> $tree
 * @return array<mixed>
 */
function format(array $tree): array
{
    return [ json_encode($tree, JSON_PRETTY_PRINT) ];
}
