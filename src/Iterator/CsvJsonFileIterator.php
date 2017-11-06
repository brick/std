<?php

declare(strict_types=1);

namespace Brick\Std\Iterator;

/**
 * Iterator to read CSV files where fields are JSON-encoded.
 *
 * This allows to support types outside of string.
 * Iterator keys are line numbers.
 */
class CsvJsonFileIterator implements \Iterator
{
    /**
     * The file pointer resource.
     *
     * @var resource
     */
    private $handle;

    /**
     * The current line.
     *
     * @var int
     */
    private $line = 1;

    /**
     * The current element as a 0-indexed array, or null if end of file / error.
     *
     * @var array|null
     */
    private $current;

    /**
     * Class constructor.
     *
     * @param string|resource $file The CSV file path, or an open file pointer.
     *
     * @throws \InvalidArgumentException If the file cannot be opened.
     */
    public function __construct($file)
    {
        if (is_resource($file)) {
            $this->handle = $file;
        } else {
            $this->handle = @ fopen($file, 'rb');

            if ($this->handle === false) {
                throw new \InvalidArgumentException('Cannot open file for reading: ' . $file);
            }
        }

        $this->current = $this->readRow();
    }

    /**
     * Reads the current row.
     *
     * If EOF is reached or an error occurs, NULL is returned.
     * If the line is empty, an empty array is returned.
     *
     * @return array|null
     */
    private function readRow() : ?array
    {
        $line = @ fgets($this->handle);

        if ($line === false) {
            return null;
        }

        $data = @ json_decode('[' . $line . ']');

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(json_last_error_msg());
        }

        return $data;
    }

    /**
     * Rewinds the Iterator to the first element.
     *
     * If the stream does not support seeking, the iterator will be left in the current position.
     *
     * @return void
     */
    public function rewind() : void
    {
        if ($this->line !== 1 && fseek($this->handle, 0) === 0) {
            $this->line = 1;
        }
    }

    /**
     * Returns whether the current position is valid.
     *
     * @return bool
     */
    public function valid() : bool
    {
        return $this->current !== null;
    }

    /**
     * Returns the key of the current element (line number).
     *
     * @return int
     */
    public function key() : int
    {
        return $this->line;
    }

    /**
     * Returns the current element, or null if end of file / error.
     *
     * @return array|null
     */
    public function current() : ?array
    {
        return $this->current;
    }

    /**
     * Move forward to next element.
     *
     * @return void
     */
    public function next() : void
    {
        $this->current = $this->readRow();
        $this->line++;
    }
}
