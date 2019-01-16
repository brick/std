<?php

declare(strict_types=1);

namespace Brick\Std\Internal;

/**
 * Catches PHP errors in a specific code block, and throws exceptions.
 *
 * This works regardless of the error_reporting option and the registered error handler.
 *
 * @internal
 */
final class ErrorCatcher
{
    /**
     * Runs the given function, catching PHP errors and throwing exceptions.
     *
     * @param callable $function The function to execute.
     *
     * @return mixed The return value of the function.
     *
     * @throws \ErrorException If a PHP error occurs.
     */
    public static function run(callable $function)
    {
        set_error_handler(static function($severity, $message, $file, $line) {
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
