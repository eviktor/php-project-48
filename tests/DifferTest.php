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
        $subTests = [
            [ '', 'file1.json', 'result_empty_file1.txt' ],
            [ 'file1.json', '', 'result_file1_empty.txt' ],
            [ '', 'file1.yml', 'result_empty_file1.txt' ],
            [ 'file1.yml', '', 'result_file1_empty.txt' ],
        ];

        foreach ($subTests as $subTest) {
            $data1 = empty($subTest[0]) ? '' : $this->getFixtureContents($subTest[0]);
            $data2 = empty($subTest[1]) ? '' : $this->getFixtureContents($subTest[1]);
            $result = genDiff($data1, $data2);
            $expectedResult = $this->getFixtureContents($subTest[2]);
            $this->assertEquals($expectedResult, $result);
        }
    }

    public function testJsonGeneral(): void
    {
        $subTests = [
            [ 'file1.json', 'file2.json', 'result_file1_file2.txt' ],
            [ 'file2.json', 'file1.json', 'result_file2_file1.txt' ],
            [ 'file1.yml', 'file2.yml', 'result_file1_file2.txt' ],
            [ 'file2.yml', 'file1.yml', 'result_file2_file1.txt' ],
        ];

        foreach ($subTests as $subTest) {
            $data1 = empty($subTest[0]) ? '' : $this->getFixtureContents($subTest[0]);
            $data2 = empty($subTest[1]) ? '' : $this->getFixtureContents($subTest[1]);
            $result = genDiff($data1, $data2);
            $expectedResult = $this->getFixtureContents($subTest[2]);
            $this->assertEquals($expectedResult, $result);
        }
    }
}
