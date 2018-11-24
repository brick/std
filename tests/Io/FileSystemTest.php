<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Io;

use Brick\Std\Io\FileSystem;

class FileSystemTest extends FileSystemTestCase
{
    /**
     * @var string
     */
    private $tmp;

    /**
     * @param string $cmd
     *
     * @return void
     */
    private function exec(string $cmd) : void
    {
        $result = system($cmd, $status);

        if ($result === false || $status !== 0) {
            throw new \RuntimeException('Failed to exec command: ' . $cmd);
        }
    }

    public function setUp()
    {
        $this->tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'FileSystemTest';

        $this->exec('rm -rf ' . $this->tmp);
        $this->exec('mkdir ' . $this->tmp);

        chdir($this->tmp);

        mkdir('./tmp');
    }

    public function tearDown()
    {
        $this->exec('rm -rf ' . $this->tmp);
        $this->exec('rm -rf ' . './tmp');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error getting real path of invalid_path, check that the path exists
     */
    public function testGetRealPathWithInvalidPath()
    {
        FileSystem::getRealPath('invalid_path');
    }

    public function testGetRealPath()
    {
        $filePath = __DIR__ . '/../../composer.json';
        $expectedRealPath = realpath($filePath);

        $this->assertSame($expectedRealPath, FileSystem::getRealPath($filePath));
    }

    public function testWriteWithAppendFlag()
    {
        FileSystem::write('./tmp/temp_file', 'data1' . PHP_EOL);
        FileSystem::write('./tmp/temp_file', 'data2', true);

        $this->assertSame('data1' . PHP_EOL . 'data2', FileSystem::read('./tmp/temp_file'));
    }

    public function testWriteWithLockFlag()
    {
        $this->assertSame(5, FileSystem::write('./tmp/temp_lock_file', '12345', false, true));
    }

    public function testReadWithMaxLength()
    {
        FileSystem::write('./tmp/temp_lock_file', 'data');

        $this->assertSame('dat', FileSystem::read('./tmp/temp_lock_file', 0, 3));
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error copying ./tmp/temp_lock_file to ./non_existing_dir/temp_lock_file
     */
    public function testCopyShouldThrowIOException()
    {
        FileSystem::write('./tmp/temp_lock_file', 'data');
        FileSystem::copy('./tmp/temp_lock_file', './non_existing_dir/temp_lock_file');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error moving ./tmp/temp_lock_file to ./non_existing_dir/temp_lock_file
     */
    public function testMoveShouldThrowIOException()
    {
        FileSystem::write('./tmp/temp_lock_file', 'data');
        FileSystem::move('./tmp/temp_lock_file', './non_existing_dir/temp_lock_file');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error deleting ./non_existing_dir/temp_lock_file
     */
    public function testDeleteShouldThrowIOException()
    {
        FileSystem::delete('./non_existing_dir/temp_lock_file');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error creating directory ./non_existing_dir/temp_lock_file
     */
    public function testCreateDirectoryShouldThrowIOException()
    {
        FileSystem::createDirectory('./non_existing_dir/temp_lock_file');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error creating directories ./tmp/new_file/temp_directory
     */
    public function testCreateDirectoriesShouldThrowIOException()
    {
        FileSystem::write('./tmp/new_file', '');
        FileSystem::createDirectories('./tmp/new_file/temp_directory');
    }

    public function testCreateDirectoriesTwice()
    {
        FileSystem::createDirectories('./tmp/temp_directory');
        FileSystem::createDirectories('./tmp/temp_directory');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error creating link ./invalid_link to ./non_existing_dir/invalid_target
     */
    public function testCreateLinkWithInvalidFileLink()
    {
        FileSystem::createLink('./invalid_link', './non_existing_dir/invalid_target');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error reading symbolic link ./invalid_path
     */
    public function testReadSymbolicLinkWithInvalidFileLink()
    {
        FileSystem::readSymbolicLink('./invalid_path');
    }

   /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error writing to ./non_existing_dir/invalid_path
     */
    public function testWriteWithInvalidFilePath()
    {
        FileSystem::write('./non_existing_dir/invalid_path', 'data');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error reading from ./non_existing_dir/invalid_path
     */
    public function testReadWithInvalidFilePath()
    {
        FileSystem::read('./non_existing_dir/invalid_path');
    }

    public function testCopy()
    {
        $this->file_put_contents('./tmp/a.txt', 'Hello World');

        FileSystem::copy('./tmp/a.txt', './tmp/b.txt');

        $this->assertFileExists('./tmp/a.txt');
        $this->assertFileExists('./tmp/b.txt');
        $this->assertFileContains('./tmp/a.txt', 'Hello World');
        $this->assertFileContains('./tmp/b.txt', 'Hello World');
    }

    public function testMove()
    {
        $this->file_put_contents('./tmp/a.txt', 'Hello World');

        FileSystem::move('./tmp/a.txt', './tmp/b.txt');

        $this->assertFileNotExists('./tmp/a.txt');
        $this->assertFileExists('./tmp/b.txt');
        $this->assertFileContains('./tmp/b.txt', 'Hello World');
    }

    public function testDeleteFile()
    {
        $this->touch('./tmp/a.txt');

        FileSystem::delete('./tmp/a.txt');

        $this->assertFileNotExists('./tmp/a.txt');
    }

    public function testDeleteDirectory()
    {
        $this->mkdir('./tmp/a');

        FileSystem::delete('./tmp/a');

        $this->assertFileNotExists('./tmp/a');
    }

    public function testCreateDirectory()
    {
        FileSystem::createDirectory('./tmp/a');

        $this->assertIsDirectory('./tmp/a');
    }

    public function testCreateDirectories()
    {
        FileSystem::createDirectories('./tmp/a/b/c');

        $this->assertIsDirectory('./tmp/a');
        $this->assertIsDirectory('./tmp/a/b');
        $this->assertIsDirectory('./tmp/a/b/c');
    }

    public function testExists()
    {
        $this->assertFalse(FileSystem::exists('./tmp/a'));
        $this->assertFalse(FileSystem::exists('./tmp/b'));

        $this->touch('./tmp/a.txt');
        $this->mkdir('./tmp/b');

        $this->assertTrue(FileSystem::exists('./tmp/a.txt'));
        $this->assertTrue(FileSystem::exists('./tmp/b'));
    }

    public function testIsFile()
    {
        $this->assertFalse(FileSystem::isFile('./tmp/a.txt'));
        $this->assertFalse(FileSystem::isFile('./tmp/b.txt'));

        $this->touch('./tmp/a.txt');
        $this->mkdir('./tmp/b');

        $this->assertTrue(FileSystem::isFile('./tmp/a.txt'));
        $this->assertFalse(FileSystem::isFile('./tmp/b'));
    }

    public function testIsDirectory()
    {
        $this->assertFalse(FileSystem::isDirectory('./tmp/a'));
        $this->assertFalse(FileSystem::isDirectory('./tmp/b'));

        $this->touch('./tmp/a');
        $this->mkdir('./tmp/b');

        $this->assertFalse(FileSystem::isDirectory('./tmp/a'));
        $this->assertTrue(FileSystem::isDirectory('./tmp/b'));
    }

    public function testIsSymbolicLink()
    {
        $this->assertFalse(FileSystem::isSymbolicLink('./tmp/a'));
        $this->assertFalse(FileSystem::isSymbolicLink('./tmp/b'));
        $this->assertFalse(FileSystem::isSymbolicLink('./tmp/c'));

        $this->touch('./tmp/a');
        $this->mkdir('./tmp/b');
        $this->symlink('./tmp/a', './tmp/c');

        $this->assertFalse(FileSystem::isSymbolicLink('./tmp/a'));
        $this->assertFalse(FileSystem::isSymbolicLink('./tmp/b'));
        $this->assertTrue(FileSystem::isSymbolicLink('./tmp/c'));
    }

    public function testCreateSymbolicLink()
    {
        $this->file_put_contents('a', 'Hello');

        FileSystem::createSymbolicLink('b', 'a');

        $this->assertIsFile('a');
        $this->assertIsSymbolicLink('./b');

        $this->assertFileContains('a', 'Hello');
        $this->assertFileContains('b', 'Hello');
    }

    public function testCreateLink()
    {
        $this->file_put_contents('./tmp/a', 'World');

        FileSystem::createLink('./tmp/b', './tmp/a');

        $this->assertFileContains('./tmp/a', 'World');
        $this->assertFileContains('./tmp/b', 'World');
    }

    public function testReadSymbolicLink()
    {
        $this->touch('a');
        $this->symlink($target = $this->tmp . DIRECTORY_SEPARATOR . 'a', './tmp/link');

        $this->assertSame($target, FileSystem::readSymbolicLink($this->tmp . DIRECTORY_SEPARATOR . './tmp/link'));
    }

    public function testWrite()
    {
        FileSystem::write('./tmp/write.txt', 'write content');

        $this->assertFileContains('./tmp/write.txt', 'write content');
    }

    public function testRead()
    {
        $this->file_put_contents('./tmp/read.txt', 'read content');

        $this->assertSame('read content', FileSystem::read('./tmp/read.txt'));
    }
}
