<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Iterator;

use Brick\Std\Iterator\CsvFileIterator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class CsvFileIterator.
 */
class CsvFileIteratorTest extends TestCase
{
    public function testConstructorWithNonExistentFile()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot open file for reading: NonExistentFile');
        new CsvFileIterator('NonExistentFile');
    }

    /**
     * @param string       $csv       The CSV input.
     * @param bool         $headerRow Whether to use the first row as column headers.
     * @param array|string $expected  The expected output, or an expected exception message.
     *
     * @return void
     */
    #[DataProvider('providerIterator')]
    public function testIterator(string $csv, bool $headerRow, $expected) : void
    {
        $fp = $this->stringToResource($csv);
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
    public static function providerIterator() : array
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

    /**
     * @return void
     */
    public function testAllowLessColumns() : void
    {
        $fp = $this->stringToResource("a,b\nc");

        $iterator = new CsvFileIterator($fp, true);
        $iterator->allowLessColumns();

        $rows = iterator_to_array($iterator);
        fclose($fp);

        $this->assertSame([['a' => 'c', 'b' => null]], $rows);
    }

    /**
     * @return void
     */
    public function testAllowMoreColumns() : void
    {
        $fp = $this->stringToResource("a\nb,c");

        $iterator = new CsvFileIterator($fp, true);
        $iterator->allowMoreColumns();

        $rows = iterator_to_array($iterator);
        fclose($fp);

        $this->assertSame([['a' => 'b']], $rows);
    }

    /**
     * @param string $string
     *
     * @return resource
     */
    private function stringToResource(string $string)
    {
        $fp = fopen('php://memory', 'rb+');

        fwrite($fp, $string);
        fseek($fp, 0);

        return $fp;
    }
}
