<?php

declare(strict_types=1);

namespace Brick\Std\Json;

/**
 * Encodes data in JSON format.
 */
final class JsonEncoder extends Common
{
    /**
     * Encodes data in JSON format.
     *
     * @param mixed $data
     *
     * @return string A JSON string representation of the data.
     *
     * @throws JsonException If the data cannot be encoded.
     */
    public function encode($data) : string
    {
        $result = json_encode($data, $this->options, $this->maxDepth);

        $this->checkLastError();

        return $result;
    }

    /**
     * Sets whether to convert `<` and `>` to `\u003C` and `\u003E`. Defaults to `false`.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function escapeTags(bool $bool) : void
    {
        $this->setOption(JSON_HEX_TAG, $bool);
    }

    /**
     * Sets whether to convert `&` to `\u0026`. Defaults to `false`.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function escapeAmpersands(bool $bool) : void
    {
        $this->setOption(JSON_HEX_AMP, $bool);
    }

    /**
     * Sets whether to convert `'` to `\u0027`. Defaults to `false`.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function escapeApostrophes(bool $bool) : void
    {
        $this->setOption(JSON_HEX_APOS, $bool);
    }

    /**
     * Sets whether to convert `"` to `\u0022`. Defaults to `false`.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function escapeQuotes(bool $bool) : void
    {
        $this->setOption(JSON_HEX_QUOT, $bool);
    }

    /**
     * Sets whether to output an object rather than an array when a non-associative array is used. Defaults to `false`.
     *
     * Especially useful when the recipient of the output is expecting an object and the array is empty.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function forceObject(bool $bool) : void
    {
        $this->setOption(JSON_FORCE_OBJECT, $bool);
    }

    /**
     * Sets whether to encode numeric strings as numbers. Defaults to `false`.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function encodeNumericStringsAsNumbers(bool $bool) : void
    {
        $this->setOption(JSON_NUMERIC_CHECK, $bool);
    }

    /**
     * Sets whether to use whitespace in returned data to format it. Defaults to `false`.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function prettyPrint(bool $bool) : void
    {
        $this->setOption(JSON_PRETTY_PRINT, $bool);
    }

    /**
     * Sets whether to escape `/`. Defaults to `true`.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function escapeSlashes(bool $bool) : void
    {
        $this->setOption(JSON_UNESCAPED_SLASHES, ! $bool);
    }

    /**
     * Sets whether to escape multibyte Unicode characters as `\uXXXX`. Defaults to `true`.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function escapeUnicode(bool $bool) : void
    {
        $this->setOption(JSON_UNESCAPED_UNICODE, ! $bool);
    }

    /**
     * Sets whether to escape multibyte Unicode line terminator characters as `\uXXXX`. Defaults to `true`.
     *
     * Note that line terminators will still be escaped if `escapeUnicode()` is set to true (default).
     *
     * @param bool $bool
     *
     * @return void
     */
    public function escapeLineTerminators(bool $bool) : void
    {
        $this->setOption(JSON_UNESCAPED_LINE_TERMINATORS, ! $bool);
    }

    /**
     * Sets whether to always encode float values as float values, even when the fraction is zero. Defaults to `false`.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function preserveZeroFraction(bool $bool) : void
    {
        $this->setOption(JSON_PRESERVE_ZERO_FRACTION, $bool);
    }
}
