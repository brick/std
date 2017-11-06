<?php

namespace Brick\Std\Tests\Iterator;

use Brick\Std\Iterator\CsvJsonFileIterator;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class CsvFileIterator.
 */
class CsvJsonFileIteratorTest extends TestCase
{
    /**
     * @dataProvider providerIterator
     *
     * @param string $csv      The CSV input.
     * @param array  $expected The expected output.
     */
    public function testIterator($csv, array $expected)
    {
        $fp = fopen('php://memory', 'r+');
        fwrite($fp, $csv);
        fseek($fp, 0);

        $iterator = new CsvJsonFileIterator($fp);
        $this->assertSame($expected, iterator_to_array($iterator));

        fclose($fp);
    }

    /**
     * @return array
     */
    public function providerIterator()
    {
        return [
            ['', []],
            ['null', [1 => [null]]],
            ['"test",null', [1 => ['test', null]]],
            ['"test",null' . "\n", [1 => ['test', null]]],
            ['"test",null' . "\n\n", [1 => ['test', null], 2 => []]],
            ['"a",1,2.0,false,null' . "\n" . '"b",2,3.0,true,"c"', [1 => ['a', 1, 2.0, false, null], 2 => ['b', 2, 3.0, true, 'c']]]
        ];
    }
}
