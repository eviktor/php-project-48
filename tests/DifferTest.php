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

    public function testEmpty(): void
    {
        $result1 = trim(genDiff($this->getFixtureFullPath('empty.json'), $this->getFixtureFullPath('empty.json')));
        $this->assertEquals("{\n}", $result1);

        // $resul2t = trim(genDiff($this->getFixtureFullPath('empty.yml'), $this->getFixtureFullPath('empty.yml')));
        // $this->assertEquals("{\n}", $result2);
    }

    public function testOneSideEmpty(): void
    {
        $subTests = [
            [ 'empty.json', 'file1.json', 'result_empty_file1.txt' ],
            [ 'file1.json', 'empty.json', 'result_file1_empty.txt' ],
            // [ 'empty.yml', 'file1.yml', 'result_empty_file1.txt' ],
            // [ 'file1.yml', 'empty.yml', 'result_file1_empty.txt' ],
        ];

        foreach ($subTests as $subTest) {
            $filePath1 = $this->getFixtureFullPath($subTest[0]);
            $filePath2 = $this->getFixtureFullPath($subTest[1]);
            $expectedResultFilePath = $this->getFixtureFullPath($subTest[2]);

            $result = genDiff($filePath1, $filePath2);
            $this->assertStringEqualsFile($expectedResultFilePath, $result);
        }
    }

    public function testJsonGeneral(): void
    {
        $subTests = [
            [ 'file1.json', 'file2.json', 'result_file1_file2.txt' ],
            [ 'file2.json', 'file1.json', 'result_file2_file1.txt' ],
            // [ 'file1.yml', 'file2.yml', 'result_file1_file2.txt' ],
            // [ 'file2.yml', 'file1.yml', 'result_file2_file1.txt' ],
        ];

        foreach ($subTests as $subTest) {
            $filePath1 = $this->getFixtureFullPath($subTest[0]);
            $filePath2 = $this->getFixtureFullPath($subTest[1]);
            $expectedResultFilePath = $this->getFixtureFullPath($subTest[2]);

            $result = genDiff($filePath1, $filePath2);
            $this->assertStringEqualsFile($expectedResultFilePath, $result);
        }
    }
}
