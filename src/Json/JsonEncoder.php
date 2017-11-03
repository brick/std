<?php

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
        return $this->execute(function() use ($data) {
            return json_encode($data, $this->options, $this->maxDepth);
        });
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
     * Sets whether to encode large integers as their original string value. Defaults to `false`.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function encodeBigIntAsString(bool $bool) : void
    {
        $this->setOption(JSON_BIGINT_AS_STRING, $bool);
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
    public function escapeMultibyteUnicode(bool $bool) : void
    {
        $this->setOption(JSON_UNESCAPED_UNICODE, ! $bool);
    }
}
