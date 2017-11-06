<?php

declare(strict_types=1);

namespace Brick\Std\Curl;

/**
 * Exception thrown when a Curl request fails.
 */
class CurlException extends \RuntimeException
{
    /**
     * @param string $error
     *
     * @return CurlException
     */
    public static function error(string $error) : CurlException
    {
        return new self(sprintf('cURL request failed: %s.', $error));
    }
}
