<?php

declare(strict_types=1);

namespace Brick\Std\Json;

use Brick\Std\ErrorCatcher;

/**
 * Common functionality for JsonEncoder and JsonDecoder.
 */
abstract class Common
{
    /**
     * The maximum encoding depth.
     *
     * @var int
     */
    protected $maxDepth = 512;

    /**
     * The encoding options bitmask.
     *
     * @var int
     */
    protected $options = 0;

    /**
     * Sets the max depth. Defaults to `512`.
     *
     * @param int $depth
     *
     * @return void
     */
    public function setMaxDepth(int $depth) : void
    {
        $this->maxDepth = $depth;
    }

    /**
     * Executes the given function and throws an exception if an error has occurred.
     *
     * @param \Closure $function The function to execute.
     *
     * @return mixed The value returned by the function.
     *
     * @throws JsonException If an error occurs.
     */
    protected function execute(\Closure $function)
    {
        try {
            $result = ErrorCatcher::run($function);
        } catch (\ErrorException $e) {
            throw JsonException::wrap($e);
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException(json_last_error_msg(), json_last_error());
        }

        return $result;
    }

    /**
     * Sets or resets a bitmask option.
     *
     * @param int  $option A JSON_* constant.
     * @param bool $bool   The boolean value.
     *
     * @return void
     */
    protected function setOption(int $option, bool $bool) : void
    {
        if ($bool) {
            $this->options |= $option;
        } else {
            $this->options &= ~ $option;
        }
    }
}
