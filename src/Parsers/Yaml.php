<?php

namespace Differ\Parsers\Yaml;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

use function Differ\Parsers\Json\parse as parseJson;

/**
 * @return array<mixed>|false
 */
function parse(string $content): array|false
{
    try {
        $data = Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
    } catch (ParseException $exception) {
        return false;
    }
    $emptyJson = '{}';
    $json = is_null($data) ? $emptyJson : json_encode($data);
    if ($json === false) {
        return false;
    }
    return parseJson($json);
}
