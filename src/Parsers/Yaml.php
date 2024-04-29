<?php

namespace Differ\Parsers\Yaml;

use function Php\Immutable\Fs\Trees\trees\mkdir;
use function Php\Immutable\Fs\Trees\trees\mkfile;

/**
 * @return array<mixed>
 */
function parse(string $content, string $rootName = ''): array
{
    return mkdir($rootName);
}
