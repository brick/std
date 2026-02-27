<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Io;

use PHPUnit\Framework\TestCase;
use RuntimeException;

use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function is_file;
use function is_link;
use function mkdir;
use function symlink;
use function touch;

/**
 * Base class for FileSystemTest.
 *
 * Native filesystem functions required for test setup are wrapped to ensure that they complete successfully before
 * running the tests, to avoid false positives should both the test setup *and* the tested method fail.
 */
abstract class FileSystemTestCase extends TestCase
{
    protected function assertFileContains(string $path, string $expected): void
    {
        $actual = @file_get_contents($path);
        self::assertSame($expected, $actual);
    }

    protected function assertIsFile(string $path): void
    {
        self::assertTrue(@is_file($path));
    }

    protected function assertIsDirectory(string $path): void
    {
        self::assertTrue(@is_dir($path));
    }

    protected function assertIsSymbolicLink(string $path): void
    {
        self::assertTrue(@is_link($path));
    }

    protected function file_put_contents(string $path, string $data): void
    {
        $result = @file_put_contents($path, $data);

        if ($result === false) {
            throw new RuntimeException('Could not write ' . $path);
        }
    }

    protected function mkdir(string $path): void
    {
        $result = @mkdir($path);

        if ($result !== true) {
            throw new RuntimeException('Could not create directory ' . $path);
        }
    }

    protected function symlink(string $target, string $link): void
    {
        $result = symlink($target, $link);

        if ($result !== true) {
            throw new RuntimeException('Could not create symlink ' . $link);
        }
    }

    protected function touch(string $path): void
    {
        $result = @touch($path);

        if ($result !== true) {
            throw new RuntimeException('Could not touch ' . $path);
        }
    }
}
