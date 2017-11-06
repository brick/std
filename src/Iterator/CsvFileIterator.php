<?php

declare(strict_types=1);

namespace Brick\Std\Iterator;

/**
 * Iterator to read CSV files.
 */
class CsvFileIterator implements \Iterator
{
    /**
     * The file pointer resource.
     *
     * @var resource
     */
    private $handle;

    /**
     * The field delimiter (one character only).
     *
     * @var string
     */
    private $delimiter;

    /**
     * The field enclosure character (one character only).
     *
     * @var string
     */
    private $enclosure;

    /**
     * The escape character (one character only).
     *
     * @var string
     */
    private $escape;

    /**
     * The key of the current element (0-based).
     *
     * @var int
     */
    private $key;

    /**
     * The current element as a 0-indexed array, or null if end of file / error.
     *
     * @var array|null
     */
    private $current;

    /**
     * @var bool
     */
    private $headerRow;

    /**
     * The column names, or null if returning numeric arrays.
     *
     * @var array|null
     */
    private $columns;

    /**
     * Class constructor.
     *
     * @param string|resource $file      The CSV file path, or an open file pointer.
     * @param bool            $headerRow Whether the first row contains the column names.
     * @param string          $delimiter The field delimiter character.
     * @param string          $enclosure The field enclosure character.
     * @param string          $escape    The escape character.
     *
     * @throws \InvalidArgumentException If the file cannot be opened.
     */
    public function __construct($file, bool $headerRow = false, string $delimiter = ',', string $enclosure = '"', string $escape = '\\')
    {
        if (is_resource($file)) {
            $this->handle = $file;
        } else {
            $this->handle = @ fopen($file, 'rb');

            if ($this->handle === false) {
                throw new \InvalidArgumentException('Cannot open file for reading: ' . $file);
            }
        }

        $this->headerRow = $headerRow;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape    = $escape;

        $this->init();
    }

    /**
     * @return void
     */
    private function init() : void
    {
        if ($this->headerRow) {
            $this->columns = $this->readRow();

            if ($this->columns === null) {
                $this->columns = [];
            }
        }

        $this->readCurrent();
        $this->key = 0;
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
        $row = @ fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure, $this->escape);

        if ($row === false || $row === null) {
            return null;
        }

        if ($row[0] === null) {
            return [];
        }

        return $row;
    }

    /**
     * Reads the current CSV row.
     *
     * @return void
     */
    private function readCurrent() : void
    {
        $row = $this->readRow();

        if ($this->columns === null || $row === null) {
            $this->current = $row;
        } else {
            $this->current = [];

            foreach ($this->columns as $key => $name) {
                $this->current[$name] = isset($row[$key]) ? $row[$key] : null;
            }
        }
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
        if ($this->key !== 0 && fseek($this->handle, 0) === 0) {
            $this->init();
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
     * Returns the key of the current element (0-based).
     *
     * @return int
     */
    public function key() : int
    {
        return $this->key;
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
        $this->readCurrent();
        $this->key++;
    }
}
