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
     * @expectedExceptionMessage Cannot open file for reading: this_file_is_not_existed
     */
    public function testConstructorWithNonExistentFile()
    {
        $iterator = new CsvFileIterator('NonExistentFile');
    }

    /**
     * @dataProvider providerIterator
     *
     * @param string $csv       The CSV input.
     * @param bool   $headerRow Whether to use the first row as column headers.
     * @param array  $expected  The expected output.
     *
     * @return void
     */
    public function testIterator(string $csv, bool $headerRow, array $expected) : void
    {
        $fp = fopen('php://memory', 'rb+');
        fwrite($fp, $csv);
        fseek($fp, 0);

        $iterator = new CsvFileIterator($fp, $headerRow);
        $this->assertSame($expected, iterator_to_array($iterator));

        fclose($fp);
    }

    /**
     * @return array
     */
    public function providerIterator() : array
    {
        return [
            ["", false, []],
            ["", true, []],
            [" ", false, [[" "]]],
            [" ", true, []],
            ["\n", false, [[]]],
            ["\n", true, []],
            ["a", false, [["a"]]],
            ["a", true, []],
            ["a\n", false, [["a"]]],
            ["a\n", true, []],
            ["a\nb", false, [["a"], ["b"]]],
            ["a\nb", true, [["a" => "b"]]],
            ["a\n\nb", false, [["a"], [], ["b"]]],
            ["a\n\nb", true, [["a" => null], ["a" => "b"]]],
            ["a,b", false, [["a", "b"]]],
            ["a,b", true, []],
            ["a,b\nc", false, [["a", "b"],["c"]]],
            ["a,b\nc", true, [["a" => "c", "b" => null]]],
            ["a\nb,c", false, [["a"], ["b", "c"]]],
            ["a\nb,c", true, [["a" => "b"]]]
        ];
    }
}
