<?php

declare(strict_types=1);

namespace Brick\Std\Io;

class IoException extends \Exception
{
    /**
     * @param \Exception $e
     *
     * @return IoException
     */
    public static function wrap(\Exception $e) : IoException
    {
        return new self($e->getMessage(), 0, $e);
    }
}
