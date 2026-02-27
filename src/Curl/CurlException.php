<?php

declare(strict_types=1);

namespace Brick\Std\Curl;

use RuntimeException;

use function sprintf;

/**
 * Exception thrown when a Curl request fails.
 */
final class CurlException extends RuntimeException
{
    public static function error(string $error): CurlException
    {
        return new self(sprintf('cURL request failed: %s.', $error));
    }
}
