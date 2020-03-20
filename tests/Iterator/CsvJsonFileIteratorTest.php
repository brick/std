<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Iterator;

use Brick\Std\Iterator\CsvJsonFileIterator;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class CsvFileIterator.
 */
class CsvJsonFileIteratorTest extends TestCase
{
    public function testConstructorWithNonExistentFile()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectDeprecationMessage('Cannot open file for reading: NonExistentFile');
        new CsvJsonFileIterator('NonExistentFile');
    }

    public function testReadRowShouldThrowRuntimeException()
    {
        $fp = fopen('php://memory', 'rb+');
        fwrite($fp, 'this,is,');
        fseek($fp, 0);

        $this->expectException(\RuntimeException::class);
        $this->expectDeprecationMessage('Syntax error');

        try {
            new CsvJsonFileIterator($fp);
        } finally {
            fclose($fp);
        }
    }

    /**
     * @dataProvider providerIterator
     *
     * @param string $csv      The CSV input.
     * @param array  $expected The expected output.
     *
     * @return void
     */
    public function testIterator(string $csv, array $expected) : void
    {
        $fp = fopen('php://memory', 'rb+');
        fwrite($fp, $csv);
        fseek($fp, 0);

        $iterator = new CsvJsonFileIterator($fp);
        $this->assertSame($expected, iterator_to_array($iterator));

        fclose($fp);
    }

    /**
     * @return array
     */
    public function providerIterator() : array
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
