<?php

declare(strict_types=1);

namespace Brick\Std\Io;

use Brick\Std\Internal\ErrorCatcher;

final class FileSystem
{
    /**
     * Copies a file.
     *
     * If the destination file already exists, it will be overwritten.
     * If the destination is an existing directory, an exception is thrown.
     *
     * @param string $source      The source path.
     * @param string $destination The destination path.
     *
     * @return void
     *
     * @throws IoException If an error occurs.
     */
    public static function copy(string $source, string $destination) : void
    {
        $exception = null;

        try {
            $success = ErrorCatcher::run(static function() use ($source, $destination) {
                return copy($source, $destination);
            });

            if ($success === true) {
                return;
            }
        } catch (\ErrorException $e) {
            $exception = $e;
        }

        throw new IoException('Error copying ' . $source . ' to ' . $destination, 0, $exception);
    }

    /**
     * Moves a file or a directory.
     *
     * If the source is a file and the destination is an existing file, it will be overwritten.
     * If the source is a directory and/or the destination is an existing directory, an exception is thrown.
     *
     * @param string $source      The source path.
     * @param string $destination The destination path.
     *
     * @return void
     *
     * @throws IoException If an error occurs.
     */
    public static function move(string $source, string $destination) : void
    {
        $exception = null;

        try {
            $success = ErrorCatcher::run(static function() use ($source, $destination) {
                return rename($source, $destination);
            });

            if ($success === true) {
                return;
            }
        } catch (\ErrorException $e) {
            $exception = $e;
        }

        throw new IoException('Error moving ' . $source . ' to ' . $destination, 0, $exception);
    }

    /**
     * Deletes a file or a directory.
     *
     * If the target is a directory, it must be empty.
     *
     * @param string $path
     *
     * @return void
     *
     * @throws IoException If an error occurs.
     */
    public static function delete(string $path) : void
    {
        $exception = null;

        try {
            $success = ErrorCatcher::run(static function() use ($path) {
                if (is_dir($path)) {
                    return rmdir($path);
                }

                return unlink($path);
            });

            if ($success === true) {
                return;
            }
        } catch (\ErrorException $e) {
            $exception = $e;
        }

        throw new IoException('Error deleting ' . $path, 0, $exception);
    }

    /**
     * Creates a directory.
     *
     * If the directory already exists, an exception is thrown.
     *
     * @param string $path The directory path.
     * @param int    $mode The access mode. The mode is 0777 by default, which means the widest possible access.
     *
     * @return void
     *
     * @throws IoException If an error occurs.
     */
    public static function createDirectory(string $path, int $mode = 0777) : void
    {
        $exception = null;

        try {
            $success = ErrorCatcher::run(static function() use ($path, $mode) {
                return mkdir($path, $mode);
            });

            if ($success === true) {
                return;
            }
        } catch (\ErrorException $e) {
            $exception = $e;
        }

        throw new IoException('Error creating directory ' . $path, 0, $exception);
    }

    /**
     * Creates a directory by creating all nonexistent parent directories first.
     *
     * Unlike the `createDirectory` method, an exception is not thrown if the directory could not be created because it
     * already exists.
     *
     * @param string $path The directory path.
     * @param int    $mode The access mode. The mode is 0777 by default, which means the widest possible access.
     *
     * @return void
     *
     * @throws IoException If an error occurs.
     */
    public static function createDirectories(string $path, int $mode = 0777) : void
    {
        $exception = null;

        try {
            $success = ErrorCatcher::run(static function() use ($path, $mode) {
                return mkdir($path, $mode, true);
            });

            if ($success === true) {
                return;
            }
        } catch (\ErrorException $e) {
            $exception = $e;
        }

        if (self::isDirectory($path)) {
            return;
        }

        throw new IoException('Error creating directories ' . $path, 0, $exception);
    }

    /**
     * Checks whether a file or directory exists.
     *
     * @param string $path The file path.
     *
     * @return bool
     *
     * @throws IoException If an error occurs.
     */
    public static function exists(string $path) : bool
    {
        try {
            return ErrorCatcher::run(static function() use ($path) {
                return file_exists($path);
            });
        } catch (\ErrorException $e) {
            throw new IoException('Error checking if ' . $path . ' exists', 0, $e);
        }
    }

    /**
     * Checks whether the path points to a regular file.
     *
     * @param string $path The file path path.
     *
     * @return bool
     *
     * @throws IoException If an error occurs.
     */
    public static function isFile(string $path) : bool
    {
        try {
            return ErrorCatcher::run(static function() use ($path) {
                return is_file($path);
            });
        } catch (\ErrorException $e) {
            throw new IoException('Error checking if ' . $path . ' is a file', 0, $e);
        }
    }

    /**
     * Checks whether the path points to a directory.
     *
     * @param string $path The file path.
     *
     * @return bool
     *
     * @throws IoException If an error occurs.
     */
    public static function isDirectory(string $path) : bool
    {
        try {
            return ErrorCatcher::run(static function() use ($path) {
                return is_dir($path);
            });
        } catch (\ErrorException $e) {
            throw new IoException('Error checking if ' . $path . ' is a directory', 0, $e);
        }
    }

    /**
     * Checks whether the path points to a symbolic link.
     *
     * @param string $path The file path.
     *
     * @return bool
     *
     * @throws IoException If an error occurs.
     */
    public static function isSymbolicLink(string $path) : bool
    {
        try {
            return ErrorCatcher::run(static function() use ($path) {
                return is_link($path);
            });
        } catch (\ErrorException $e) {
            throw new IoException('Error checking if ' . $path . ' is a symbolic link', 0, $e);
        }
    }

    /**
     * Creates a symbolic link to a target.
     *
     * @param string $link   The path of the symbolic link to create.
     * @param string $target The target of the symbolic link.
     *
     * @return void
     *
     * @throws IoException If an error occurs.
     */
    public static function createSymbolicLink(string $link, string $target) : void
    {
        $exception = null;

        try {
            $success = ErrorCatcher::run(static function() use ($link, $target) {
                return symlink($target, $link);
            });

            if ($success === true) {
                return;
            }
        } catch (\ErrorException $e) {
            $exception = $e;
        }

        throw new IoException('Error creating symbolic link ' . $link . ' to ' . $target, 0, $exception);
    }

    /**
     * Creates a hard link to an existing file.
     *
     * @param string $link   The path of the symbolic link to create.
     * @param string $target The path of an existing file.
     *
     * @return void
     *
     * @throws IoException If an error occurs.
     */
    public static function createLink(string $link, string $target) : void
    {
        $exception = null;

        try {
            $success = ErrorCatcher::run(static function() use ($link, $target) {
                return link($target, $link);
            });

            if ($success === true) {
                return;
            }
        } catch (\ErrorException $e) {
            $exception = $e;
        }

        throw new IoException('Error creating link ' . $link . ' to ' . $target, 0, $exception);
    }

    /**
     * Returns the target of a symbolic link.
     *
     * @param string $path The symbolic link path.
     *
     * @return string The contents of the symbolic link path.
     *
     * @throws IoException If an error occurs.
     */
    public static function readSymbolicLink(string $path) : string
    {
        $exception = null;

        try {
            $result = ErrorCatcher::run(static function() use ($path) {
                return readlink($path);
            });

            if ($result !== false) {
                return $result;
            }
        } catch (\ErrorException $e) {
            $exception = $e;
        }

        throw new IoException('Error reading symbolic link ' . $path, 0, $exception);
    }

    /**
     * Returns the canonicalized absolute pathname.
     *
     * @param string $path The path.
     *
     * @return string The canonicalized absolute pathname.
     *
     * @throws IoException If an error occurs.
     */
    public static function getRealPath(string $path) : string
    {
        $exception = null;

        try {
            $result = ErrorCatcher::run(static function() use ($path) {
                return realpath($path);
            });

            if ($result !== false) {
                return $result;
            }
        } catch (\ErrorException $e) {
            $exception = $e;
        }

        throw new IoException('Error getting real path of ' . $path . ', check that the path exists', 0, $exception);
    }

    /**
     * Writes data to a file.
     *
     * If the file already exists, it will be overwritten, unless `$append` is set to `true`.
     *
     * @param string          $path  The path to the file.
     * @param string|resource $data  The data to write, as a string or a stream resource.
     * @param bool            $append Whether to append if the file already exists. Defaults to false (overwrite).
     * @param bool            $lock   Whether to acquire an exclusive lock on the file. Defaults to false.
     *
     * @return int The number of bytes written.
     *
     * @throws IoException If an error occurs.
     */
    public static function write(string $path, $data, bool $append = false, bool $lock = false) : int
    {
        $flags = 0;

        if ($append) {
            $flags |= FILE_APPEND;
        }
        if ($lock) {
            $flags |= LOCK_EX;
        }

        $exception = null;

        try {
            $result = ErrorCatcher::run(static function() use ($path, $data, $flags) {
                return file_put_contents($path, $data, $flags);
            });

            if ($result !== false) {
                return $result;
            }
        } catch (\ErrorException $e) {
            $exception = $e;
        }

        throw new IoException('Error writing to ' . $path, 0, $exception);
    }

    /**
     * Reads data from a file.
     *
     * Always be careful when reading a file in memory, as it may exceed the memory limit.
     *
     * @param string   $path      The path to the file.
     * @param int      $offset    The offset where the reading starts on the original stream.
     *                            Negative offsets count from the end of the stream.
     * @param int|null $maxLength Maximum length of data read. The default is to read until end of file is reached.
     *
     * @return string The file contents.
     *
     * @throws IoException If an error occurs.
     */
    public static function read(string $path, int $offset = 0, int $maxLength = null) : string
    {
        $exception = null;

        try {
            $result = ErrorCatcher::run(static function() use ($path, $offset, $maxLength) {
                if ($maxLength === null) {
                    return file_get_contents($path, false, null, $offset);
                }

                return file_get_contents($path, false, null, $offset, $maxLength);
            });

            if ($result !== false) {
                return $result;
            }
        } catch (\ErrorException $e) {
            $exception = $e;
        }

        throw new IoException('Error reading from ' . $path, 0, $exception);
    }
}
