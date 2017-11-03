<?php

declare(strict_types=1);

namespace Brick\Std;

/**
 * Catches PHP errors in a specific code block, and throws exceptions.
 *
 * This works regardless of the error_reporting option and the registered error handler.
 */
final class ErrorCatcher
{
    /**
     * @param callable $function The function to execute.
     *
     * @return mixed The return value of the function.
     *
     * @throws \ErrorException If a PHP error occurs.
     */
    public static function tryCatch(callable $function)
    {
        set_error_handler(function($severity, $message, $file, $line) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        try {
            $result = $function();
        } finally {
            restore_error_handler();
        }

        return $result;
    }
}
