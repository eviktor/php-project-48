<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

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
        $emptyResult = "{\n}";
        $result1 = trim(genDiff($this->getFixtureFullPath('empty.json'), $this->getFixtureFullPath('empty.json')));
        $this->assertEquals($emptyResult, $result1);

        $result2 = trim(genDiff($this->getFixtureFullPath('empty.yml'), $this->getFixtureFullPath('empty.yml')));
        $this->assertEquals($emptyResult, $result2);
    }

    /**
     * @return array<mixed>
     */
    public static function oneSideProvider(): array
    {
        return [
            'json left empty'   => [ 'empty.json', 'file1.json', 'result_empty_file1.txt' ],
            'json right empty'  => [ 'file1.json', 'empty.json', 'result_file1_empty.txt' ],
            'yaml left empty'   => [ 'empty.yml', 'file1.yml', 'result_empty_file1.txt' ],
            'yaml right empty'  => [ 'file1.yml', 'empty.yml', 'result_file1_empty.txt' ],
        ];
    }

    #[DataProvider('oneSideProvider')]
    public function testOneSideEmpty(string $fileName1, string $fileName2, string $expectedResultFileName): void
    {
        $filePath1 = $this->getFixtureFullPath($fileName1);
        $filePath2 = $this->getFixtureFullPath($fileName2);
        $expectedResultFilePath = $this->getFixtureFullPath($expectedResultFileName);

        $result = genDiff($filePath1, $filePath2);
        $this->assertStringEqualsFile($expectedResultFilePath, $result);
    }

    /**
     * @return array<mixed>
     */
    public static function generalProvider(): array
    {
        return [
            'json 1,2' => [ 'file1.json', 'file2.json', 'result_file1_file2.txt' ],
            'json 2,1' => [ 'file2.json', 'file1.json', 'result_file2_file1.txt', 'stylish' ],
            'yaml 1,2' => [ 'file1.yml', 'file2.yml', 'result_file1_file2.txt', 'stylish' ],
            'yaml 2,1' => [ 'file2.yml', 'file1.yml', 'result_file2_file1.txt' ],
            'json 1,2 plain' => [ 'file1.json', 'file2.json', 'result_file1_file2_plain.txt', 'plain' ],
            'yaml 1,2 json' => [ 'file1.yml', 'file2.yml', 'result_file1_file2_json.txt', 'json' ],
        ];
    }

    #[DataProvider('generalProvider')]
    public function testGeneral(
        string $fileName1,
        string $fileName2,
        string $expectedResultFileName,
        ?string $style = null
    ): void {
        $filePath1 = $this->getFixtureFullPath($fileName1);
        $filePath2 = $this->getFixtureFullPath($fileName2);
        $expectedResultFilePath = $this->getFixtureFullPath($expectedResultFileName);

        $result = $style ? genDiff($filePath1, $filePath2, $style) : genDiff($filePath1, $filePath2);
        $this->assertStringEqualsFile($expectedResultFilePath, $result);
    }
}
