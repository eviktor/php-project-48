<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

function removeSpaces(string $str): string
{
    return (string)preg_replace('/[ |\t]+/', ' ', trim($str));
}

class DifferTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->assertEquals('', genDiff('', ''));
    }

    public function testOneSideEmpty(): void
    {
        $json = '{ "host": "hexlet.io", "timeout": 50, "proxy": "123.234.53.22", "follow": false }';

        $result1 = removeSpaces(genDiff($json, ''));
        $expectedResult1 = removeSpaces(
            '{
                + follow: false
                + host: hexlet.io
                + proxy: 123.234.53.22
                + timeout: 50
            }'
        );
        $this->assertEquals($expectedResult1, $result1);

        $result2 = removeSpaces(genDiff('', $json));
        $expectedResult2 = removeSpaces(
            '{
                - follow: false
                - host: hexlet.io
                - proxy: 123.234.53.22
                - timeout: 50
            }'
        );
        $this->assertEquals($expectedResult2, $result2);
    }

    public function testGeneral(): void
    {
        $json1 = '{ "host": "hexlet.io", "timeout": 50, "proxy": "123.234.53.22", "follow": false }';
        $json2 = '{ "timeout": 20, "verbose": true, "host": "hexlet.io" }';
        $result = removeSpaces(genDiff($json1, $json2));
        $expectedResult = removeSpaces(
            '{
                - follow: false
                host: hexlet.io
                - proxy: 123.234.53.22
                - timeout: 50
                + timeout: 20
                + verbose: true
            }'
        );
        $this->assertEquals($expectedResult, $result);
    }
}
