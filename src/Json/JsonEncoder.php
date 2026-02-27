<?php

declare(strict_types=1);

namespace Brick\Std\Json;

use function json_encode;

use const JSON_FORCE_OBJECT;
use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_NUMERIC_CHECK;
use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_LINE_TERMINATORS;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * Encodes data in JSON format.
 */
final class JsonEncoder extends Common
{
    /**
     * Encodes data in JSON format.
     *
     * @return string A JSON string representation of the data.
     *
     * @throws JsonException If the data cannot be encoded.
     */
    public function encode(mixed $data): string
    {
        $result = json_encode($data, $this->options, $this->maxDepth);

        $this->checkLastError();

        return $result;
    }

    /**
     * Sets whether to convert `<` and `>` to `\u003C` and `\u003E`. Defaults to `false`.
     */
    public function escapeTags(bool $bool): void
    {
        $this->setOption(JSON_HEX_TAG, $bool);
    }

    /**
     * Sets whether to convert `&` to `\u0026`. Defaults to `false`.
     */
    public function escapeAmpersands(bool $bool): void
    {
        $this->setOption(JSON_HEX_AMP, $bool);
    }

    /**
     * Sets whether to convert `'` to `\u0027`. Defaults to `false`.
     */
    public function escapeApostrophes(bool $bool): void
    {
        $this->setOption(JSON_HEX_APOS, $bool);
    }

    /**
     * Sets whether to convert `"` to `\u0022`. Defaults to `false`.
     */
    public function escapeQuotes(bool $bool): void
    {
        $this->setOption(JSON_HEX_QUOT, $bool);
    }

    /**
     * Sets whether to output an object rather than an array when a non-associative array is used. Defaults to `false`.
     *
     * Especially useful when the recipient of the output is expecting an object and the array is empty.
     */
    public function forceObject(bool $bool): void
    {
        $this->setOption(JSON_FORCE_OBJECT, $bool);
    }

    /**
     * Sets whether to encode numeric strings as numbers. Defaults to `false`.
     */
    public function encodeNumericStringsAsNumbers(bool $bool): void
    {
        $this->setOption(JSON_NUMERIC_CHECK, $bool);
    }

    /**
     * Sets whether to use whitespace in returned data to format it. Defaults to `false`.
     */
    public function prettyPrint(bool $bool): void
    {
        $this->setOption(JSON_PRETTY_PRINT, $bool);
    }

    /**
     * Sets whether to escape `/`. Defaults to `true`.
     */
    public function escapeSlashes(bool $bool): void
    {
        $this->setOption(JSON_UNESCAPED_SLASHES, ! $bool);
    }

    /**
     * Sets whether to escape multibyte Unicode characters as `\uXXXX`. Defaults to `true`.
     */
    public function escapeUnicode(bool $bool): void
    {
        $this->setOption(JSON_UNESCAPED_UNICODE, ! $bool);
    }

    /**
     * Sets whether to escape multibyte Unicode line terminator characters as `\uXXXX`. Defaults to `true`.
     *
     * Note that line terminators will still be escaped if `escapeUnicode()` is set to true (default).
     */
    public function escapeLineTerminators(bool $bool): void
    {
        $this->setOption(JSON_UNESCAPED_LINE_TERMINATORS, ! $bool);
    }

    /**
     * Sets whether to always encode float values as float values, even when the fraction is zero. Defaults to `false`.
     */
    public function preserveZeroFraction(bool $bool): void
    {
        $this->setOption(JSON_PRESERVE_ZERO_FRACTION, $bool);
    }
}
