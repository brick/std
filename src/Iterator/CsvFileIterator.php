<?php

declare(strict_types=1);

namespace Brick\Std\Iterator;

/**
 * Iterator to read CSV files.
 *
 * Supports CSV files with & without header rows.
 * Skips empty lines, apart from the header row which must be the first line in the file.
 */
class CsvFileIterator implements \IteratorAggregate
{
    /**
     * The file pointer resource.
     *
     * @var resource
     */
    private $handle;

    /**
     * Whether the first row contains column names.
     *
     * - When true, rows will be returned as associative arrays of column name to value; every row must have the same
     *   number of columns, or an exception is thrown; empty rows are skipped.
     * - When false, rows will be returned as numeric arrays; rows are allowed to have different numbers of columns;
     *   empty rows are returned as empty arrays.
     *
     * @var bool
     */
    private $headerRow;

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
     * Class constructor.
     *
     * @param string|resource $file      The CSV file path, or an open file pointer.
     *                                   If a file pointer is given, it must be at the beginning of the file.
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
    }

    /**
     * @return \Generator
     *
     * @throws \RuntimeException If a header row is configured but the header row is empty.
     */
    public function getIterator() : \Generator
    {
        $columnNames = [];
        $columnCount = 0;
        $startLine   = 1;

        if ($this->headerRow) {
            $columnNames = $this->readRow();

            if ($columnNames === null) {
                throw new \RuntimeException('Expected header row, found EOF.');
            }

            $this->checkColumnNames($columnNames);
            $columnCount = count($columnNames);

            if ($columnCount === 0) {
                throw new \RuntimeException('Empty header line.');
            }

            $startLine = 2;
        }

        for ($line = $startLine; ; $line++) {
            $row = $this->readRow();

            if ($row === null) { // EOF
                return;
            }

            $rowColumnCount = count($row);

            if ($rowColumnCount === 0) {
                // skip empty lines
                continue;
            }

            if ($columnNames) {
                if ($rowColumnCount !== $columnCount) {
                    throw new \RuntimeException(sprintf(
                        'Expected %d columns on line %d, found %d.',
                        $columnCount,
                        $line,
                        $rowColumnCount
                    ));
                }

                yield array_combine($columnNames, $row);
            } else {
                yield $row;
            }
        }
    }

    /**
     * Checks column names for empty names & duplicates.
     *
     * @param string[] $columnNames
     *
     * @return void
     */
    private function checkColumnNames(array $columnNames) : void
    {
        $processedNames = [];

        foreach ($columnNames as $key => $columnName) {
            $columnNumber = $key + 1;

            if ($columnName === '') {
                throw new \RuntimeException(sprintf('Empty column name at column %d.', $columnNumber));
            }

            if (isset($processedNames[$columnName])) {
                throw new \RuntimeException(sprintf(
                    'Duplicate column name at columns %d and %d.',
                    $processedNames[$columnName],
                    $columnNumber
                ));
            }

            $processedNames[$columnName] = $columnNumber;
        }
    }

    /**
     * Reads one row from the CSV file.
     *
     * If EOF is reached, NULL is returned.
     * If the line is empty, an empty array is returned.
     *
     * @return array|null
     *
     * @throws \RuntimeException If the file handle is invalid.
     */
    private function readRow() : ?array
    {
        $row = @ fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure, $this->escape);

        // fgetcsv() returns NULL if an invalid handle is supplied...
        if ($row === null) {
            throw new \RuntimeException('Invalid file handle.');
        }

        // ...or FALSE on other errors, including end of file.
        if ($row === false) {
            return null;
        }

        // A blank line in a CSV file will be returned as an array comprising a single null field,
        // and will not be treated as an error.
        if ($row[0] === null) {
            return [];
        }

        return $row;
    }
}
