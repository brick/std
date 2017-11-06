<?php

namespace Brick\Std\Tests\Iterator;

use Brick\Std\Iterator\CsvFileIterator;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class CsvFileIterator.
 */
class CsvFileIteratorTest extends TestCase
{
    /**
     * @dataProvider providerIterator
     *
     * @param string  $csv       The CSV input.
     * @param boolean $headerRow Whether to use the first row as column headers.
     * @param array   $expected  The expected output.
     */
    public function testIterator($csv, $headerRow, array $expected)
    {
        $fp = fopen('php://memory', 'r+');
        fwrite($fp, $csv);
        fseek($fp, 0);

        $iterator = new CsvFileIterator($fp, $headerRow);
        $this->assertSame($expected, iterator_to_array($iterator));

        fclose($fp);
    }

    /**
     * @return array
     */
    public function providerIterator()
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
