<?php

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
     * @param integer $index1 The index of the first entry.
     * @param integer $index2 The index of the second entry.
     *
     * @return static This FixedArray instance.
     */
    public function swap($index1, $index2)
    {
        if ($index1 != $index2) {
            $value = $this[$index1];
            $this[$index1] = $this[$index2];
            $this[$index2] = $value;
        }

        return $this;
    }

    /**
     * Shifts an entry to the next index.
     *
     * This will effectively swap this entry with the next entry.
     * If this entry is the last one in the array, this method will do nothing.
     *
     * @param integer $index
     *
     * @return static
     */
    public function shiftUp($index)
    {
        if ($index + 1 == $this->count()) {
            return $this;
        }

        return $this->swap($index, $index + 1);
    }

    /**
     * Shifts an entry to the previous index.
     *
     * This will effectively swap this entry with the previous entry.
     * If the entry is the first one in the array, this method will do nothing.
     *
     * @param integer $index
     *
     * @return static
     */
    public function shiftDown($index)
    {
        if ($index == 0) {
            return $this;
        }

        return $this->swap($index, $index - 1);
    }

    /**
     * Shifts an entry to an arbitrary index, shifting all the entries between those indexes.
     *
     * @param integer $index    The index of the entry.
     * @param integer $newIndex The index to shift the entry to.
     *
     * @return static
     */
    public function shiftTo($index, $newIndex)
    {
        while ($index > $newIndex) {
            $this->shiftDown($index);
            $index--;
        }
        while ($index < $newIndex) {
            $this->shiftUp($index);
            $index++;
        }

        return $this;
    }
}
