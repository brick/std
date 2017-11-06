<?php

declare(strict_types=1);

namespace Brick\Std;

/**
 * Extends SplFixedArray with convenient methods to move entries.
 */
class FixedArray extends \SplFixedArray
{
    /**
     * Imports a PHP array in a FixedArray instance.
     *
     * This method needs to be reimplemented as SplFixedArray does not return `new static`.
     * @see https://bugs.php.net/bug.php?id=55128
     *
     * Subclasses of FixedArray do not need to reimplement this method.
     *
     * @param array   $array
     * @param boolean $saveIndexes
     *
     * @return FixedArray
     *
     * @throws \InvalidArgumentException If the array contains non-numeric or negative indexes.
     */
    public static function fromArray($array, $saveIndexes = true)
    {
        $splFixedArray = \SplFixedArray::fromArray($array, $saveIndexes);

        $result = new static($splFixedArray->count());
        $source = $saveIndexes ? $array : $splFixedArray;

        foreach ($source as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Swaps two entries in this FixedArray.
     *
     * @param int $index1 The index of the first entry.
     * @param int $index2 The index of the second entry.
     *
     * @return void
     */
    public function swap(int $index1, int $index2) : void
    {
        if ($index1 != $index2) {
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
     *
     * @param int $index
     *
     * @return void
     */
    public function shiftUp(int $index) : void
    {
        if ($index + 1 == $this->count()) {
            return;
        }

        $this->swap($index, $index + 1);
    }

    /**
     * Shifts an entry to the previous index.
     *
     * This will effectively swap this entry with the previous entry.
     * If the entry is the first one in the array, this method will do nothing.
     *
     * @param int $index
     *
     * @return void
     */
    public function shiftDown(int $index) : void
    {
        if ($index == 0) {
            return;
        }

        $this->swap($index, $index - 1);
    }

    /**
     * Shifts an entry to an arbitrary index, shifting all the entries between those indexes.
     *
     * @param int $index    The index of the entry.
     * @param int $newIndex The index to shift the entry to.
     *
     * @return void
     */
    public function shiftTo(int $index, int $newIndex) : void
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
