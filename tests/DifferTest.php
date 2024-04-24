<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function getFixtureFullPath(string $fixtureName): string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        $path = realpath(implode('/', $parts));
        return $path === false ? '' : $path;
    }

    public function getFixtureContents(string $fixtureName): string
    {
        $contents = file_get_contents($this->getFixtureFullPath($fixtureName));
        return $contents === false ? '' : $contents;
    }

    public function testEmpty(): void
    {
        $result = trim(genDiff('', ''));
        $this->assertEquals("{\n}", $result);
    }

    public function testOneSideEmpty(): void
    {
        $json = $this->getFixtureContents('file1.json');

        $result1 = genDiff('', $json);
        $expectedResult1 = $this->getFixtureContents('result_empty_file1.txt');
        $this->assertEquals($expectedResult1, $result1);

        $result2 = genDiff($json, '');
        $expectedResult2 = $this->getFixtureContents('result_file1_empty.txt');
        $this->assertEquals($expectedResult2, $result2);
    }

    public function testGeneral(): void
    {
        $json1 = $this->getFixtureContents('file1.json');
        $json2 = $this->getFixtureContents('file2.json');

        $result1 = genDiff($json1, $json2);
        $expectedResult1 = $this->getFixtureContents('result_file1_file2.txt');
        $this->assertEquals($expectedResult1, $result1);

        $result2 = genDiff($json2, $json1);
        $expectedResult2 = $this->getFixtureContents('result_file2_file1.txt');
        $this->assertEquals($expectedResult2, $result2);
    }
}
