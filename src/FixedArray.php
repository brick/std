<?php

declare(strict_types=1);

namespace Brick\Std;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use SplFixedArray;
use Traversable;

/**
 * An array of fixed length.
 *
 * This class internally wraps SplFixedArray.
 */
final class FixedArray implements Countable, IteratorAggregate, ArrayAccess
{
    private SplFixedArray $splFixedArray;

    /**
     * Class constructor.
     */
    public function __construct(SplFixedArray $fixedArray)
    {
        $this->splFixedArray = $fixedArray;
    }

    public static function create(int $size = 0): FixedArray
    {
        return new FixedArray(new SplFixedArray($size));
    }

    /**
     * Creates a FixedArray from a PHP array.
     *
     * @throws InvalidArgumentException If the array contains non-numeric or negative indexes.
     */
    public static function fromArray(array $array, bool $saveIndexes = true): FixedArray
    {
        return new FixedArray(SplFixedArray::fromArray($array, $saveIndexes));
    }

    public function toArray(): array
    {
        return $this->splFixedArray->toArray();
    }

    /**
     * Returns the size of the array.
     */
    public function getSize(): int
    {
        return $this->splFixedArray->getSize();
    }

    public function setSize(int $size): void
    {
        $this->splFixedArray->setSize($size);
    }

    /**
     * Returns the size of the array.
     *
     * This is an alias of getSize(), required by interface Countable.
     */
    public function count(): int
    {
        return $this->splFixedArray->count();
    }

    /**
     * Returns whether or not an offset exists.
     *
     * Required by interface ArrayAccess.
     *
     * @param int $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->splFixedArray->offsetExists($offset);
    }

    /**
     * Returns the value at specified offset.
     *
     * Required by interface ArrayAccess.
     *
     * @param int $offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->splFixedArray->offsetGet($offset);
    }

    /**
     * Assigns a value to the specified offset.
     *
     * Required by interface ArrayAccess.
     *
     * @param int $offset
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->splFixedArray->offsetSet($offset, $value);
    }

    /**
     * Unsets an offset.
     *
     * Required by interface ArrayAccess.
     *
     * @param int $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->splFixedArray->offsetUnset($offset);
    }

    /**
     * Returns an iterator for this fixed array.
     *
     * Required by interface IteratorAggregate.
     */
    public function getIterator(): Traversable
    {
        return $this->splFixedArray;
    }

    /**
     * Swaps two entries in this FixedArray.
     *
     * @param int $index1 The index of the first entry.
     * @param int $index2 The index of the second entry.
     */
    public function swap(int $index1, int $index2): void
    {
        if ($index1 !== $index2) {
            $value = $this[$index1];
            $this[$index1] = $this[$index2];
            $this[$index2] = $value;
        }
    }

    /**
     * Shifts an entry to the next index.
     *
     * This will effectively swap this entry with the next entry.
     * If this entry is the last one in the array, this method will do nothing.
     */
    public function shiftUp(int $index): void
    {
        if ($index + 1 === $this->count()) {
            return;
        }

        $this->swap($index, $index + 1);
    }

    /**
     * Shifts an entry to the previous index.
     *
     * This will effectively swap this entry with the previous entry.
     * If the entry is the first one in the array, this method will do nothing.
     */
    public function shiftDown(int $index): void
    {
        if ($index === 0) {
            return;
        }

        $this->swap($index, $index - 1);
    }

    /**
     * Shifts an entry to an arbitrary index, shifting all the entries between those indexes.
     *
     * @param int $index    The index of the entry.
     * @param int $newIndex The index to shift the entry to.
     */
    public function shiftTo(int $index, int $newIndex): void
    {
        while ($index > $newIndex) {
            $this->shiftDown($index);
            $index--;
        }
        while ($index < $newIndex) {
            $this->shiftUp($index);
            $index++;
        }
    }
}
