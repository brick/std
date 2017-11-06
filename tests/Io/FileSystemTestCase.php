<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Io;

use PHPUnit\Framework\TestCase;

/**
 * Base class for FileSystemTest.
 *
 * Native filesystem functions required for test setup are wrapped to ensure that they complete successfully before
 * running the tests, to avoid false positives should both the test setup *and* the tested method fail.
 */
abstract class FileSystemTestCase extends TestCase
{
    /**
     * @param string $path
     * @param string $expected
     *
     * @return void
     */
    protected function assertFileContains(string $path, string $expected) : void
    {
        $actual = @ file_get_contents($path);
        $this->assertSame($expected, $actual);
    }

    /**
     * @param string $path
     *
     * @return void
     */
    protected function assertIsFile(string $path) : void
    {
        $this->assertTrue(@ is_file($path));
    }

    /**
     * @param string $path
     *
     * @return void
     */
    protected function assertIsDirectory(string $path) : void
    {
        $this->assertTrue(@ is_dir($path));
    }

    /**
     * @param string $path
     *
     * @return void
     */
    protected function assertIsSymbolicLink(string $path) : void
    {
        $this->assertTrue(@ is_link($path));
    }

    /**
     * @param string $path
     * @param string $data
     *
     * @return void
     */
    protected function file_put_contents(string $path, string $data) : void
    {
        $result = @ file_put_contents($path, $data);

        if ($result === false) {
            throw new \RuntimeException('Could not write ' . $path);
        }
    }

    /**
     * @param string $path
     *
     * @return void
     */
    protected function mkdir(string $path) : void
    {
        $result = @ mkdir($path);

        if ($result !== true) {
            throw new \RuntimeException('Could not create directory ' . $path);
        }
    }

    /**
     * @param string $target
     * @param string $link
     *
     * @return void
     */
    protected function symlink(string $target, string $link) : void
    {
        $result = symlink($target, $link);

        if ($result !== true) {
            throw new \RuntimeException('Could not create symlink ' . $link);
        }
    }

    /**
     * @param string $path
     *
     * @return void
     */
    protected function touch(string $path) : void
    {
        $result = @ touch($path);

        if ($result !== true) {
            throw new \RuntimeException('Could not touch ' . $path);
        }
    }
}
