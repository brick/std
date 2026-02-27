<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Io;

use Brick\Std\Io\FileSystem;
use Brick\Std\Io\IoException;
use RuntimeException;

use function chdir;
use function realpath;
use function sys_get_temp_dir;
use function system;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

class FileSystemTest extends FileSystemTestCase
{
    private string $tmp;

    public function setUp(): void
    {
        $this->tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'FileSystemTest';

        $this->exec('rm -rf ' . $this->tmp);
        $this->exec('mkdir ' . $this->tmp);

        chdir($this->tmp);
    }

    public function tearDown(): void
    {
        $this->exec('rm -rf ' . $this->tmp);
    }

    public function testGetRealPathWithInvalidPath(): void
    {
        $this->expectException(IoException::class);
        $this->expectExceptionMessage('Error getting real path of invalid_path, check that the path exists');
        FileSystem::getRealPath('invalid_path');
    }

    public function testGetRealPath(): void
    {
        $filePath = __DIR__ . '/../../composer.json';
        $expectedRealPath = realpath($filePath);

        self::assertSame($expectedRealPath, FileSystem::getRealPath($filePath));
    }

    public function testWriteWithAppendFlag(): void
    {
        FileSystem::write('temp_file', 'data1' . PHP_EOL);
        FileSystem::write('temp_file', 'data2', true);

        self::assertSame('data1' . PHP_EOL . 'data2', FileSystem::read('temp_file'));
    }

    public function testWriteWithLockFlag(): void
    {
        self::assertSame(5, FileSystem::write('temp_lock_file', '12345', false, true));
    }

    public function testReadWithMaxLength(): void
    {
        FileSystem::write('temp_lock_file', 'data');

        self::assertSame('dat', FileSystem::read('temp_lock_file', 0, 3));
    }

    public function testCopyShouldThrowIOException(): void
    {
        FileSystem::write('temp_lock_file', 'data');

        $this->expectException(IoException::class);
        $this->expectExceptionMessage('Error copying temp_lock_file to non_existing_dir/temp_lock_file');
        FileSystem::copy('temp_lock_file', 'non_existing_dir/temp_lock_file');
    }

    public function testMoveShouldThrowIOException(): void
    {
        FileSystem::write('temp_lock_file', 'data');

        $this->expectException(IoException::class);
        $this->expectExceptionMessage('Error moving temp_lock_file to non_existing_dir/temp_lock_file');
        FileSystem::move('temp_lock_file', 'non_existing_dir/temp_lock_file');
    }

    public function testDeleteShouldThrowIOException(): void
    {
        $this->expectException(IoException::class);
        $this->expectExceptionMessage('Error deleting non_existing_dir/temp_lock_file');
        FileSystem::delete('non_existing_dir/temp_lock_file');
    }

    public function testCreateDirectoryShouldThrowIOException(): void
    {
        $this->expectException(IoException::class);
        $this->expectExceptionMessage('Error creating directory non_existing_dir/temp_lock_file');
        FileSystem::createDirectory('non_existing_dir/temp_lock_file');
    }

    public function testCreateDirectoriesShouldThrowIOException(): void
    {
        FileSystem::write('new_file', '');

        $this->expectException(IoException::class);
        $this->expectExceptionMessage('Error creating directories new_file/temp_directory');
        FileSystem::createDirectories('new_file/temp_directory');
    }

    public function testCreateDirectoriesTwice(): void
    {
        FileSystem::createDirectories('temp_directory');
        FileSystem::createDirectories('temp_directory');

        self::addToAssertionCount(1);
    }

    public function testCreateLinkWithInvalidFileLink(): void
    {
        $this->expectException(IoException::class);
        $this->expectExceptionMessage('Error creating link invalid_link to non_existing_dir/invalid_target');
        FileSystem::createLink('invalid_link', 'non_existing_dir/invalid_target');
    }

    public function testReadSymbolicLinkWithInvalidFileLink(): void
    {
        $this->expectException(IoException::class);
        $this->expectExceptionMessage('Error reading symbolic link invalid_path');
        FileSystem::readSymbolicLink('invalid_path');
    }

    public function testWriteWithInvalidFilePath(): void
    {
        $this->expectException(IoException::class);
        $this->expectExceptionMessage('Error writing to non_existing_dir/invalid_path');
        FileSystem::write('non_existing_dir/invalid_path', 'data');
    }

    public function testReadWithInvalidFilePath(): void
    {
        $this->expectException(IoException::class);
        $this->expectExceptionMessage('Error reading from non_existing_dir/invalid_path');
        FileSystem::read('non_existing_dir/invalid_path');
    }

    public function testCopy(): void
    {
        $this->file_put_contents('a', 'Hello World');

        FileSystem::copy('a', 'b');

        self::assertFileExists('a');
        self::assertFileExists('b');
        $this->assertFileContains('a', 'Hello World');
        $this->assertFileContains('b', 'Hello World');
    }

    public function testMove(): void
    {
        $this->file_put_contents('a', 'Hello World');

        FileSystem::move('a', 'b');

        self::assertFileDoesNotExist('a');
        self::assertFileExists('b');
        $this->assertFileContains('b', 'Hello World');
    }

    public function testDeleteFile(): void
    {
        $this->touch('a');

        FileSystem::delete('a');

        self::assertFileDoesNotExist('a');
    }

    public function testDeleteDirectory(): void
    {
        $this->mkdir('a');

        FileSystem::delete('a');

        self::assertFileDoesNotExist('a');
    }

    public function testCreateDirectory(): void
    {
        FileSystem::createDirectory('a');

        $this->assertIsDirectory('a');
    }

    public function testCreateDirectories(): void
    {
        FileSystem::createDirectories('a/b/c');

        $this->assertIsDirectory('a');
        $this->assertIsDirectory('a/b');
        $this->assertIsDirectory('a/b/c');
    }

    public function testExists(): void
    {
        self::assertFalse(FileSystem::exists('a'));
        self::assertFalse(FileSystem::exists('b'));

        $this->touch('a');
        $this->mkdir('b');

        self::assertTrue(FileSystem::exists('a'));
        self::assertTrue(FileSystem::exists('b'));
    }

    public function testIsFile(): void
    {
        self::assertFalse(FileSystem::isFile('a'));
        self::assertFalse(FileSystem::isFile('b'));

        $this->touch('a');
        $this->mkdir('b');

        self::assertTrue(FileSystem::isFile('a'));
        self::assertFalse(FileSystem::isFile('b'));
    }

    public function testIsDirectory(): void
    {
        self::assertFalse(FileSystem::isDirectory('a'));
        self::assertFalse(FileSystem::isDirectory('b'));

        $this->touch('a');
        $this->mkdir('b');

        self::assertFalse(FileSystem::isDirectory('a'));
        self::assertTrue(FileSystem::isDirectory('b'));
    }

    public function testIsSymbolicLink(): void
    {
        self::assertFalse(FileSystem::isSymbolicLink('a'));
        self::assertFalse(FileSystem::isSymbolicLink('b'));
        self::assertFalse(FileSystem::isSymbolicLink('c'));

        $this->touch('a');
        $this->mkdir('b');
        $this->symlink('a', 'c');

        self::assertFalse(FileSystem::isSymbolicLink('a'));
        self::assertFalse(FileSystem::isSymbolicLink('b'));
        self::assertTrue(FileSystem::isSymbolicLink('c'));
    }

    public function testCreateSymbolicLink(): void
    {
        $this->file_put_contents('a', 'Hello');

        FileSystem::createSymbolicLink('b', 'a');

        $this->assertIsFile('a');
        $this->assertIsSymbolicLink('b');

        $this->assertFileContains('a', 'Hello');
        $this->assertFileContains('b', 'Hello');
    }

    public function testCreateLink(): void
    {
        $this->file_put_contents('a', 'World');

        FileSystem::createLink('b', 'a');

        $this->assertFileContains('a', 'World');
        $this->assertFileContains('b', 'World');
    }

    public function testReadSymbolicLink(): void
    {
        $this->touch('a');
        $this->symlink($target = $this->tmp . DIRECTORY_SEPARATOR . 'a', 'link');

        self::assertSame($target, FileSystem::readSymbolicLink($this->tmp . DIRECTORY_SEPARATOR . 'link'));
    }

    public function testWrite(): void
    {
        FileSystem::write('write', 'write content');

        $this->assertFileContains('write', 'write content');
    }

    public function testRead(): void
    {
        $this->file_put_contents('read', 'read content');

        self::assertSame('read content', FileSystem::read('read'));
    }

    private function exec(string $cmd): void
    {
        $result = system($cmd, $status);

        if ($result === false || $status !== 0) {
            throw new RuntimeException('Failed to exec command: ' . $cmd);
        }
    }
}
