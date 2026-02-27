<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Iterator;

use Brick\Std\Iterator\CsvFileIterator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function fclose;
use function fopen;
use function fseek;
use function fwrite;
use function is_array;
use function is_string;
use function iterator_to_array;

/**
 * Unit tests for class CsvFileIterator.
 */
class CsvFileIteratorTest extends TestCase
{
    public function testConstructorWithNonExistentFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot open file for reading: NonExistentFile');
        new CsvFileIterator('NonExistentFile');
    }

    /**
     * @param string       $csv       The CSV input.
     * @param bool         $headerRow Whether to use the first row as column headers.
     * @param array|string $expected  The expected output, or an expected exception message.
     */
    #[DataProvider('providerIterator')]
    public function testIterator(string $csv, bool $headerRow, $expected): void
    {
        $fp = $this->stringToResource($csv);
        $iterator = new CsvFileIterator($fp, $headerRow);

        if (is_string($expected)) {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage($expected);
        }

        try {
            $rows = iterator_to_array($iterator);
        } finally {
            fclose($fp);
        }

        if (is_array($expected)) {
            self::assertSame($expected, $rows);
        }
    }

    public static function providerIterator(): array
    {
        return [
            ['', false, []],
            ['', true, 'Expected header row, found EOF.'],
            [' ', false, [[' ']]],
            [' ', true, []],
            ["\n\r\n", false, []],
            ["\n", true, 'Empty header line.'],
            ['a', false, [['a']]],
            ['a', true, []],
            ["a\n", false, [['a']]],
            ["a\n", true, []],
            ["a\nb", false, [['a'], ['b']]],
            ["a\nb", true, [['a' => 'b']]],
            ["a\n\nb", false, [['a'], ['b']]],
            ["a\n\nb", true, [['a' => 'b']]],
            ['a,b', false, [['a', 'b']]],
            ['a,b', true, []],
            ["a,b\nc", false, [['a', 'b'], ['c']]],
            ["a,b\nc", true, 'Expected 2 columns on line 2, found 1.'],
            ["a\nb,c", false, [['a'], ['b', 'c']]],
            ["a\nb,c", true, 'Expected 1 columns on line 2, found 2.'],
        ];
    }

    public function testAllowLessColumns(): void
    {
        $fp = $this->stringToResource("a,b\nc");

        $iterator = new CsvFileIterator($fp, true);
        $iterator->allowLessColumns();

        $rows = iterator_to_array($iterator);
        fclose($fp);

        self::assertSame([['a' => 'c', 'b' => null]], $rows);
    }

    public function testAllowMoreColumns(): void
    {
        $fp = $this->stringToResource("a\nb,c");

        $iterator = new CsvFileIterator($fp, true);
        $iterator->allowMoreColumns();

        $rows = iterator_to_array($iterator);
        fclose($fp);

        self::assertSame([['a' => 'b']], $rows);
    }

    /**
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
