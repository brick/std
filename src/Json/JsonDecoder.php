<?php

declare(strict_types=1);

namespace Brick\Std\Json;

/**
 * Decodes data in JSON format.
 */
final class JsonDecoder extends Common
{
    /**
     * Whether to decode objects as associative arrays.
     *
     * @var bool
     */
    private $decodeObjectAsArray = false;

    /**
     * Decodes data in JSON format.
     *
     * @param string $json The JSON string to decode.
     *
     * @return mixed The decoded data.
     *
     * @throws JsonException If the data cannot be decoded.
     */
    public function decode(string $json)
    {
        // max depth is 0+ for json_encode(), and 1+ for json_decode()
        $result = json_decode($json, $this->decodeObjectAsArray, $this->maxDepth + 1, $this->options);

        $this->checkLastError();

        return $result;
    }

    /**
     * Sets whether to decode objects as associative arrays. Defaults to `false`.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function decodeObjectAsArray(bool $bool) : void
    {
        $this->decodeObjectAsArray = $bool;
    }

    /**
     * Sets whether to decode large integers as strings. Defaults to `false`.
     *
     * * `true` decodes large integers as strings
     * * `false` decodes large integers as floats
     *
     * @param bool $bool
     *
     * @return void
     */
    public function decodeBigIntAsString(bool $bool) : void
    {
        $this->setOption(JSON_BIGINT_AS_STRING, $bool);
    }
}
