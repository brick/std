<?php

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

    public function testDelete()
    {
        $this->touch('a');

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
        $this->symlink('a', 'link');

        $this->assertSame('a', FileSystem::readSymbolicLink('link'));
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
