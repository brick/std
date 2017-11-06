<?php

declare(strict_types=1);

namespace Brick\Std\Json;

/**
 * Common functionality for JsonEncoder and JsonDecoder.
 */
abstract class Common
{
    /**
     * The maximum encoding/decoding depth.
     *
     * @var int
     */
    protected $maxDepth = 512;

    /**
     * The options bitmask.
     *
     * @var int
     */
    protected $options = 0;

    /**
     * Sets the max recursion depth. Defaults to `512`.
     *
     * Every nested array or object adds one level of recursion.
     * If the max depth is zero, only scalars can be encoded/decoded.
     *
     * @param int $maxDepth
     *
     * @return void
     *
     * @throws \InvalidArgumentException If the max depth is out of range.
     */
    public function setMaxDepth(int $maxDepth) : void
    {
        if ($maxDepth < 0 || $maxDepth >= 0x7fffffff) { // max depth + 1 must not be greater than this limit
            throw new \InvalidArgumentException('Invalid max depth.');
        }

        $this->maxDepth = $maxDepth;
    }

    /**
     * Throws an exception if a JSON error has occurred.
     *
     * @return void
     *
     * @throws JsonException
     */
    protected function checkLastError(): void
    {
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException(json_last_error_msg(), json_last_error());
        }
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
