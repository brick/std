<?php

namespace Brick\Std\Json;

/**
 * Exception thrown when an error occurs during encoding/decoding in JSON format.
 */
class JsonException extends \RuntimeException
{
    /**
     * @param \Exception $e
     *
     * @return JsonException
     */
    public static function wrap(\Exception $e) : JsonException
    {
        return new self($e->getMessage(), 0, $e);
    }
}
