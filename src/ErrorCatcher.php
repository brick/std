<?php

namespace Brick\Std;

/**
 * A transient error handler mechanism to catch PHP errors in a specific code block.
 * This allows to execute functions that natively trigger PHP errors, while catching these errors
 * and not having to use the @ error suppression character, which blindly ignores any error.
 */
class ErrorCatcher
{
    /**
     * The transient error handler that will be registered every time the swallow() method is called.
     *
     * @var callable
     */
    private $transientErrorHandler;

    /**
     * The previous error handler to send errors to if an error caught does not match the given severity.
     *
     * @var callable|null
     */
    private $previousErrorHandler = null;

    /**
     * The severity of the errors to be swallowed.
     *
     * @var integer
     */
    private $severity = 0;

    /**
     * Class constructor.
     *
     * @param callable|null $handler A function that will receive an ErrorException when an error occurs.
     */
    public function __construct(callable $handler = null)
    {
        $this->transientErrorHandler = function($severity, $message, $file, $line, $context) use ($handler) {
            if ($this->severity & $severity) {
                if ($handler) {
                    $exception = new \ErrorException($message, 0, $severity, $file, $line);
                    call_user_func($handler, $exception);
                }
            } elseif ($this->previousErrorHandler) {
                call_user_func($this->previousErrorHandler, $severity, $message, $file, $line, $context);
            } else {
                return false;
            }

            return true;
        };
    }

    /**
     * Executes the given function, while swallowing errors of the given severity.
     *
     * Errors caught matching the given severity will be swallowed (the current/default error handler
     * will not be triggered), and converted to an ErrorException which will then be passed to the
     * handler set up in the constructor, if any.
     *
     * Errors caught not matching the given severity will trigger the current error handler,
     * or the default error handler if none is set. This is what would happen if the code was
     * executed outside of the swallow() method.
     *
     * @param integer  $severity The severity of the errors to catch.
     * @param callable $function The function to call. Must not have parameters.
     *
     * @return mixed
     */
    public function swallow($severity, callable $function)
    {
        $this->severity = $severity;
        $this->previousErrorHandler = set_error_handler($this->transientErrorHandler);

        $result = $function();
        restore_error_handler();

        return $result;
    }
}
