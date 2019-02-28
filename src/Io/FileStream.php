<?php

declare(strict_types=1);

namespace Brick\Std\Io;

use Brick\Std\Internal\ErrorCatcher;

class FileStream
{
    /**
     * @var resource
     */
    private $handle;

    /**
     * Whether the stream is closed.
     *
     * @var bool
     */
    private $closed = false;

    /**
     * FileStream constructor.
     *
     * @param string        $filename
     * @param string        $mode
     * @param bool          $useIncludePath
     * @param resource|null $context
     *
     * @throws IoException If an error occurs.
     */
    public function __construct(string $filename, string $mode, bool $useIncludePath = false, $context = null)
    {
        try {
            $handle = ErrorCatcher::run(static function() use ($filename, $mode, $useIncludePath, $context) {
                if ($context === null) {
                    return fopen($filename, $mode, $useIncludePath);
                } else {
                    return fopen($filename, $mode, $useIncludePath, $context);
                }
            });
        } catch (\ErrorException $e) {
            throw new IoException($e->getMessage(), 0, $e);
        }

        if ($handle === false) {
            throw new IoException('Failed to open stream.');
        }

        $this->handle = $handle;
    }

    /**
     * FileStream destructor.
     */
    public function __destruct()
    {
        try {
            $this->close();
        } catch (IoException $e) {
            // Fail silently. Destructor must not throw an exception.
        }
    }

    /**
     * @return bool
     *
     * @throws IoException If an error occurs.
     */
    public function eof() : bool
    {
        if ($this->closed) {
            throw new IoException('The stream is closed.');
        }

        try {
            return ErrorCatcher::run(function() {
                return feof($this->handle);
            });
        } catch (\ErrorException $e) {
            throw new IoException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Reads a line from the stream.
     *
     * @param int|null $maxLength The max length in bytes to read. If no max length is specified, it will keep reading
     *                            from the stream until it reaches the end of the line or EOF.
     *
     * @return string The data, or an empty string if there is no more data to read.
     *
     * @throws IoException If an error occurs.
     */
    public function gets(?int $maxLength = null) : string
    {
        if ($this->closed) {
            throw new IoException('The stream is closed.');
        }

        try {
            $data = ErrorCatcher::run(function() use ($maxLength) {
                if ($maxLength === null) {
                    return fgets($this->handle);
                } else {
                    return fgets($this->handle, $maxLength + 1);
                }
            });
        } catch (\ErrorException $e) {
            throw new IoException($e->getMessage(), 0, $e);
        }

        if ($data === false) {
            return '';
        }

        return $data;
    }

    /**
     * Reads from the stream.
     *
     * @param int $maxLength The max length in bytes to read.
     *
     * @return string The bytes read, or an empty string if there is no more data to read.
     *
     * @throws IoException If an error occurs.
     */
    public function read(int $maxLength) : string
    {
        if ($this->closed) {
            throw new IoException('The stream is closed.');
        }

        try {
            $data = ErrorCatcher::run(function() use ($maxLength) {
                return fread($this->handle, $maxLength);
            });
        } catch (\ErrorException $e) {
            throw new IoException($e->getMessage(), 0, $e);
        }

        if ($data === false) {
            return '';
        }

        return $data;
    }

    /**
     * Writes to the stream.
     *
     * @param string $data The data to write.
     *
     * @return void
     *
     * @throws IoException If an error occurs.
     */
    public function write(string $data) : void
    {
        if ($this->closed) {
            throw new IoException('The stream is closed.');
        }

        try {
            $result = ErrorCatcher::run(function() use ($data) {
                return fwrite($this->handle, $data);
            });
        } catch (\ErrorException $e) {
            throw new IoException($e->getMessage(), 0, $e);
        }

        if ($result === false) {
            throw new IoException('Failed to write to stream.');
        }
    }

    /**
     * Obtains an advisory lock.
     *
     * @param bool $exclusive
     *
     * @return void
     *
     * @throws IoException If an error occurs.
     */
    public function lock(bool $exclusive) : void
    {
        if ($this->closed) {
            throw new IoException('The stream is closed.');
        }

        try {
            $result = ErrorCatcher::run(function() use ($exclusive) {
                return flock($this->handle, $exclusive ? LOCK_EX : LOCK_SH);
            });
        } catch (\ErrorException $e) {
            throw new IoException($e->getMessage(), 0, $e);
        }

        if ($result === false) {
            throw new IoException('Failed to obtain a lock.');
        }
    }

    /**
     * Releases an advisory lock.
     *
     * @return void
     *
     * @throws IoException
     */
    public function unlock() : void
    {
        if ($this->closed) {
            throw new IoException('The stream is closed.');
        }

        try {
            $result = ErrorCatcher::run(function() {
                return flock($this->handle, LOCK_UN);
            });
        } catch (\ErrorException $e) {
            throw new IoException($e->getMessage(), 0, $e);
        }

        if ($result === false) {
            throw new IoException('Failed to release the lock.');
        }
    }

    /**
     * @return void
     *
     * @throws IoException If an error occurs.
     */
    public function close() : void
    {
        if ($this->closed) {
            throw new IoException('The stream has already been closed.');
        }

        try {
            $result = ErrorCatcher::run(function() {
                return fclose($this->handle);
            });
        } catch (\ErrorException $e) {
            throw new IoException($e->getMessage(), 0, $e);
        }

        if ($result === false) {
            throw new IoException('Failed to close the stream.');
        }

        $this->closed = true;
    }
}
