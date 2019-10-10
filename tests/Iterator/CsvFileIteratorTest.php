<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Iterator;

use Brick\Std\Iterator\CsvFileIterator;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class CsvFileIterator.
 */
class CsvFileIteratorTest extends TestCase
{
    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Cannot open file for reading: NonExistentFile
     */
    public function testConstructorWithNonExistentFile()
    {
        new CsvFileIterator('NonExistentFile');
    }

    /**
     * @dataProvider providerIterator
     *
     * @param string       $csv       The CSV input.
     * @param bool         $headerRow Whether to use the first row as column headers.
     * @param array|string $expected  The expected output, or an expected exception message.
     *
     * @return void
     */
    public function testIterator(string $csv, bool $headerRow, $expected) : void
    {
        $fp = fopen('php://memory', 'rb+');
        fwrite($fp, $csv);
        fseek($fp, 0);

        $iterator = new CsvFileIterator($fp, $headerRow);

        if (is_string($expected)) {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage($expected);
        }

        try {
            $rows = iterator_to_array($iterator);
        } finally {
            fclose($fp);
        }

        if (is_array($expected)) {
            $this->assertSame($expected, $rows);
        }
    }

    /**
     * @return array
     */
    public function providerIterator() : array
    {
        return [
            ["", false, []],
            ["", true, 'Expected header row, found EOF.'],
            [" ", false, [[" "]]],
            [" ", true, []],
            ["\n\r\n", false, []],
            ["\n", true, 'Empty header line.'],
            ["a", false, [["a"]]],
            ["a", true, []],
            ["a\n", false, [["a"]]],
            ["a\n", true, []],
            ["a\nb", false, [["a"], ["b"]]],
            ["a\nb", true, [["a" => "b"]]],
            ["a\n\nb", false, [["a"], ["b"]]],
            ["a\n\nb", true, [["a" => "b"]]],
            ["a,b", false, [["a", "b"]]],
            ["a,b", true, []],
            ["a,b\nc", false, [["a", "b"],["c"]]],
            ["a,b\nc", true, 'Expected 2 columns on line 2, found 1.'],
            ["a\nb,c", false, [["a"], ["b", "c"]]],
            ["a\nb,c", true, 'Expected 1 columns on line 2, found 2.']
        ];
    }
}
