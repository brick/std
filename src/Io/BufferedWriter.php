<?php

declare(strict_types=1);

namespace Brick\Std\Io;

/**
 * Writes data to a file stream, buffering writes to yield optimal performance.
 *
 * Do not forget to call flush() after all write()s have been performed.
 */
class BufferedWriter
{
    /**
     * The file stream to write to.
     *
     * @var FileStream
     */
    private $stream;

    /**
     * The buffer size, in bytes.
     *
     * @var int
     */
    private $size;

    /**
     * The buffer contents.
     *
     * @var string
     */
    private $buffer = '';

    /**
     * BufferedWriter constructor.
     *
     * @param FileStream $stream The file stream to write to.
     * @param int        $size   The buffer will be flushed when it reaches this size. Defaults to 1 MiB.
     */
    public function __construct(FileStream $stream, int $size = 1024 * 1024)
    {
        $this->stream = $stream;
        $this->size   = $size;
    }

    /**
     * @param string $data
     *
     * @return void
     *
     * @throws IoException If an error occurs.
     */
    public function write(string $data) : void
    {
        $this->buffer .= $data;

        if (strlen($this->buffer) >= $this->size) {
            $this->flush();
        }
    }

    /**
     * @return void
     *
     * @throws IoException If an error occurs.
     */
    public function flush() : void
    {
        $this->stream->write($this->buffer);
        $this->buffer = '';
    }
}
