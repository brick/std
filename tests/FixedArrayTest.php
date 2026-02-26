<?php

declare(strict_types=1);

namespace Brick\Std\Tests;

use Brick\Std\FixedArray;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class FixedArray.
 */
class FixedArrayTest extends TestCase
{
    /**
     * @param array $source      The source array.
     * @param bool  $saveIndexes Whether to use the save indexes functionality.
     * @param array $expected    The expected result array.
     *
     * @return void
     */
    #[DataProvider('providerFromArray')]
    public function testFromArray(array $source, bool $saveIndexes, array $expected) : void
    {
        $fixedArray = FixedArray::fromArray($source, $saveIndexes);

        $this->assertInstanceOf(FixedArray::class, $fixedArray);
        $this->assertSame($expected, iterator_to_array($fixedArray));
    }

    /**
     * @return array
     */
    public static function providerFromArray() : array
    {
        return [
            [['2' => 'x', 4 => 'y'], false, ['x', 'y']],
            [[2 => 'x', '4' => 'y'], true, [null, null, 'x', null, 'y']]
        ];
    }

    /**
     * @param array $source      The source array.
     * @param bool  $saveIndexes Whether to use the save indexes functionality.
     *
     * @return void
     */
    #[DataProvider('providerFromInvalidArrayThrowsException')]
    public function testFromInvalidArrayThrowsException(array $source, bool $saveIndexes) : void
    {
        $this->expectException(\InvalidArgumentException::class);
        FixedArray::fromArray($source);
    }

    /**
     * @return array
     */
    public static function providerFromInvalidArrayThrowsException() : array
    {
        return [
            [['x' => 'y'], false],
            [['x' => 'y'], true],
            [[-1 => 'z'], false],
            [[-1 => 'z'], true]
        ];
    }

    /**
     * @param array $source   The source array.
     * @param int   $index1   The index of the first entry.
     * @param int   $index2   The index of the second entry.
     * @param array $expected The expected result array.
     *
     * @return void
     */
    #[DataProvider('providerSwap')]
    public function testSwap(array $source, int $index1, int $index2, array $expected) : void
    {
        $fixedArray = FixedArray::fromArray($source);

        $fixedArray->swap($index1, $index2);
        $this->assertSame($expected, iterator_to_array($fixedArray));
    }

    /**
     * @return array
     */
    public static function providerSwap() : array
    {
        return [
            [['a', 'b', 'c'], 0, 0, ['a', 'b', 'c']],
            [['a', 'b', 'c'], 0, 1, ['b', 'a', 'c']],
            [['a', 'b', 'c'], 0, 2, ['c', 'b', 'a']],
            [['a', 'b', 'c'], 1, 0, ['b', 'a', 'c']],
            [['a', 'b', 'c'], 1, 1, ['a', 'b', 'c']],
            [['a', 'b', 'c'], 1, 2, ['a', 'c', 'b']],
            [['a', 'b', 'c'], 2, 0, ['c', 'b', 'a']],
            [['a', 'b', 'c'], 2, 1, ['a', 'c', 'b']],
            [['a', 'b', 'c'], 2, 2, ['a', 'b', 'c']]
        ];
    }

    /**
     * @param array $source   The source array.
     * @param int   $index    The index of the entry to shift.
     * @param array $expected The expected result array.
     *
     * @return void
     */
    #[DataProvider('providerShiftUp')]
    public function testShiftUp(array $source, int $index, array $expected) : void
    {
        $fixedArray = FixedArray::fromArray($source);

        $fixedArray->shiftUp($index);
        $this->assertSame($expected, iterator_to_array($fixedArray));
    }

    /**
     * @return array
     */
    public static function providerShiftUp() : array
    {
        return [
            [['a', 'b', 'c', 'd', 'e'], 0, ['b', 'a', 'c', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 1, ['a', 'c', 'b', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 2, ['a', 'b', 'd', 'c', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 3, ['a', 'b', 'c', 'e', 'd']],
            [['a', 'b', 'c', 'd', 'e'], 4, ['a', 'b', 'c', 'd', 'e']],
        ];
    }

    /**
     * @param array $source   The source array.
     * @param int   $index    The index of the entry to shift.
     * @param array $expected The expected result array.
     *
     * @return void
     */
    #[DataProvider('providerShiftDown')]
    public function testShiftDown(array $source, int $index, array $expected) : void
    {
        $fixedArray = FixedArray::fromArray($source);

        $fixedArray->shiftDown($index);
        $this->assertSame($expected, iterator_to_array($fixedArray));
    }

    /**
     * @return array
     */
    public static function providerShiftDown() : array
    {
        return [
            [['a', 'b', 'c', 'd', 'e'], 0, ['a', 'b', 'c', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 1, ['b', 'a', 'c', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 2, ['a', 'c', 'b', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 3, ['a', 'b', 'd', 'c', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 4, ['a', 'b', 'c', 'e', 'd']],
        ];
    }

    public function testCreate()
    {
        $fixedArray = FixedArray::create(4);

        $this->assertInstanceOf(FixedArray::class, $fixedArray);
        $this->assertSame(4, $fixedArray->getSize());
    }

    public function testToArray()
    {
        $expectedArray = [1, 2, 3, 4];
        $fixedArray = FixedArray::fromArray($expectedArray);
        $result = $fixedArray->toArray();

        $this->assertSame($expectedArray, $result);
    }

    public function testGetSize()
    {
        $fixedArray = FixedArray::fromArray([1, 2, 3, 4]);

        $this->assertSame(4, $fixedArray->getSize());
    }

    public function testSetSize()
    {
        $fixedArray = FixedArray::fromArray([1, 2, 3, 4]);
        $fixedArray->setSize(5);

        $this->assertSame(5, $fixedArray->getSize());
    }

    public static function offsetExistsProvider()
    {
        return [
            [
                [1, 2, 3, 4], 0, true
            ],
            [
                [1, 2, 3, 4], 5, false
            ],
        ];
    }

    #[DataProvider('offsetExistsProvider')]
    public function testOffsetExists($array, $index, $expected)
    {
        $fixedArray = FixedArray::fromArray($array);

        $this->assertSame($expected, $fixedArray->offsetExists($index));
    }

    public function testOffsetUnset()
    {
        $fixedArray = FixedArray::fromArray([1, 2, 3, 4]);
        $fixedArray->offsetUnset(3);

        $this->assertFalse($fixedArray->offsetExists(3));
    }

    /**
     * @param array $source   The source array.
     * @param int   $index    The index of the entry.
     * @param int   $newIndex The index to shift the entry to.
     * @param array $expected The expected result array.
     *
     * @return void
     */
    #[DataProvider('providerShiftTo')]
    public function testShiftTo(array $source, int $index, int $newIndex, array $expected) : void
    {
        $fixedArray = FixedArray::fromArray($source);

        $fixedArray->shiftTo($index, $newIndex);
        $this->assertSame($expected, iterator_to_array($fixedArray));
    }

    /**
     * @return array
     */
    public static function providerShiftTo() : array
    {
        return [
            [['a', 'b', 'c', 'd', 'e'], 0, 0, ['a', 'b', 'c', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 0, 1, ['b', 'a', 'c', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 0, 2, ['b', 'c', 'a', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 0, 3, ['b', 'c', 'd', 'a', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 0, 4, ['b', 'c', 'd', 'e', 'a']],
            [['a', 'b', 'c', 'd', 'e'], 1, 0, ['b', 'a', 'c', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 1, 1, ['a', 'b', 'c', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 1, 2, ['a', 'c', 'b', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 1, 3, ['a', 'c', 'd', 'b', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 1, 4, ['a', 'c', 'd', 'e', 'b']],
            [['a', 'b', 'c', 'd', 'e'], 2, 0, ['c', 'a', 'b', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 2, 1, ['a', 'c', 'b', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 2, 2, ['a', 'b', 'c', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 2, 3, ['a', 'b', 'd', 'c', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 2, 4, ['a', 'b', 'd', 'e', 'c']],
            [['a', 'b', 'c', 'd', 'e'], 3, 0, ['d', 'a', 'b', 'c', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 3, 1, ['a', 'd', 'b', 'c', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 3, 2, ['a', 'b', 'd', 'c', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 3, 3, ['a', 'b', 'c', 'd', 'e']],
            [['a', 'b', 'c', 'd', 'e'], 3, 4, ['a', 'b', 'c', 'e', 'd']],
            [['a', 'b', 'c', 'd', 'e'], 4, 0, ['e', 'a', 'b', 'c', 'd']],
            [['a', 'b', 'c', 'd', 'e'], 4, 1, ['a', 'e', 'b', 'c', 'd']],
            [['a', 'b', 'c', 'd', 'e'], 4, 2, ['a', 'b', 'e', 'c', 'd']],
            [['a', 'b', 'c', 'd', 'e'], 4, 3, ['a', 'b', 'c', 'e', 'd']],
            [['a', 'b', 'c', 'd', 'e'], 4, 4, ['a', 'b', 'c', 'd', 'e']],
        ];
    }
}
