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
    }

    public function tearDown()
    {
        $this->exec('rm -rf ' . $this->tmp);
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
        FileSystem::write('./temp_file', 'data1' . PHP_EOL);
        FileSystem::write('./temp_file', 'data2', true);

        $this->assertSame('data1' . PHP_EOL . 'data2', FileSystem::read('./temp_file'));
    }

    public function testWriteWithLockFlag()
    {
        $this->assertSame(5, FileSystem::write('./temp_lock_file', '12345', false, true));
    }

    public function testReadWithMaxLength()
    {
        FileSystem::write('./temp_lock_file', 'data');

        $this->assertSame('dat', FileSystem::read('./temp_lock_file', 0, 3));
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error copying ./temp_lock_file to ./non_existed_dir/temp_lock_file
     */
    public function testCopyShouldThrowIOException()
    {
        FileSystem::write('./temp_lock_file', 'data');
        FileSystem::copy('./temp_lock_file', './non_existed_dir/temp_lock_file');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error moving ./temp_lock_file to ./non_existed_dir/temp_lock_file
     */
    public function testMoveShouldThrowIOException()
    {
        FileSystem::write('./temp_lock_file', 'data');
        FileSystem::move('./temp_lock_file', './non_existed_dir/temp_lock_file');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error deleting ./non_existed_dir/temp_lock_file
     */
    public function testDeleteShouldThrowIOException()
    {
        FileSystem::delete('./non_existed_dir/temp_lock_file');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error creating directory ./non_existed_dir/temp_lock_file
     */
    public function testCreateDirectoryShouldThrowIOException()
    {
        FileSystem::createDirectory('./non_existed_dir/temp_lock_file');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error creating directories ./new_file/temp_directory
     */
    public function testCreateDirectoriesShouldThrowIOException()
    {
        FileSystem::write('./new_file', '');
        FileSystem::createDirectories('./new_file/temp_directory');
    }

    public function testCreateDirectoriesTwice()
    {
        FileSystem::createDirectories('./temp_directory');
        FileSystem::createDirectories('./temp_directory');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error creating link ./invalid_link to ./non_existed_dir/invalid_target
     */
    public function testCreateLinkWithInvalidFileLink()
    {
        FileSystem::createLink('./invalid_link', './non_existed_dir/invalid_target');
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
     * @expectedExceptionMessage Error writing to ./non_existed_dir/invalid_path
     */
    public function testWriteWithInvalidFilePath()
    {
        FileSystem::write('./non_existed_dir/invalid_path', 'data');
    }

    /**
     * @expectedException        Brick\Std\Io\IoException
     * @expectedExceptionMessage Error reading from ./non_existed_dir/invalid_path
     */
    public function testReadWithInvalidFilePath()
    {
        FileSystem::read('./non_existed_dir/invalid_path');
    }

    public function testCopy()
    {
        $this->file_put_contents('a', 'Hello World');

        FileSystem::copy('a', 'b');

        $this->assertFileExists('a');
        $this->assertFileExists('b');
        $this->assertFileContains('a', 'Hello World');
        $this->assertFileContains('b', 'Hello World');
    }

    public function testMove()
    {
        $this->file_put_contents('a', 'Hello World');

        FileSystem::move('a', 'b');

        $this->assertFileNotExists('a');
        $this->assertFileExists('b');
        $this->assertFileContains('b', 'Hello World');
    }

    public function testDeleteFile()
    {
        $this->touch('a');

        FileSystem::delete('a');

        $this->assertFileNotExists('a');
    }

    public function testDeleteDirectory()
    {
        $this->mkdir('a');

        FileSystem::delete('a');

        $this->assertFileNotExists('a');
    }

    public function testCreateDirectory()
    {
        FileSystem::createDirectory('a');

        $this->assertIsDirectory('a');
    }

    public function testCreateDirectories()
    {
        FileSystem::createDirectories('a/b/c');

        $this->assertIsDirectory('a');
        $this->assertIsDirectory('a/b');
        $this->assertIsDirectory('a/b/c');
    }

    public function testExists()
    {
        $this->assertFalse(FileSystem::exists('a'));
        $this->assertFalse(FileSystem::exists('b'));

        $this->touch('a');
        $this->mkdir('b');

        $this->assertTrue(FileSystem::exists('a'));
        $this->assertTrue(FileSystem::exists('b'));
    }

    public function testIsFile()
    {
        $this->assertFalse(FileSystem::isFile('a'));
        $this->assertFalse(FileSystem::isFile('b'));

        $this->touch('a');
        $this->mkdir('b');

        $this->assertTrue(FileSystem::isFile('a'));
        $this->assertFalse(FileSystem::isFile('b'));
    }

    public function testIsDirectory()
    {
        $this->assertFalse(FileSystem::isDirectory('a'));
        $this->assertFalse(FileSystem::isDirectory('b'));

        $this->touch('a');
        $this->mkdir('b');

        $this->assertFalse(FileSystem::isDirectory('a'));
        $this->assertTrue(FileSystem::isDirectory('b'));
    }

    public function testIsSymbolicLink()
    {
        $this->assertFalse(FileSystem::isSymbolicLink('a'));
        $this->assertFalse(FileSystem::isSymbolicLink('b'));
        $this->assertFalse(FileSystem::isSymbolicLink('c'));

        $this->touch('a');
        $this->mkdir('b');
        $this->symlink('a', 'c');

        $this->assertFalse(FileSystem::isSymbolicLink('a'));
        $this->assertFalse(FileSystem::isSymbolicLink('b'));
        $this->assertTrue(FileSystem::isSymbolicLink('c'));
    }

    public function testCreateSymbolicLink()
    {
        $this->file_put_contents('a', 'Hello');

        FileSystem::createSymbolicLink('b', 'a');

        $this->assertIsFile('a');
        $this->assertIsSymbolicLink('b');

        $this->assertFileContains('a', 'Hello');
        $this->assertFileContains('b', 'Hello');
    }

    public function testCreateLink()
    {
        $this->file_put_contents('a', 'World');

        FileSystem::createLink('b', 'a');

        $this->assertFileContains('a', 'World');
        $this->assertFileContains('b', 'World');
    }

    public function testReadSymbolicLink()
    {
        $this->touch('a');
        $this->symlink($target = $this->tmp . DIRECTORY_SEPARATOR . 'a', 'link');

        $this->assertSame($target, FileSystem::readSymbolicLink($this->tmp . DIRECTORY_SEPARATOR . 'link'));
    }

    public function testWrite()
    {
        FileSystem::write('write', 'write content');

        $this->assertFileContains('write', 'write content');
    }

    public function testRead()
    {
        $this->file_put_contents('read', 'read content');

        $this->assertSame('read content', FileSystem::read('read'));
    }
}
