<?php

namespace Brick\Std\Tests;

use Brick\Std\ObjectStorage;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class ObjectStorage.
 */
class ObjectStorageTest extends TestCase
{
    /**
     * @var object
     */
    private static $a;

    /**
     * @var object
     */
    private static $b;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass() : void
    {
        self::$a = new \StdClass();
        self::$b = new \StdClass();
    }

    /**
     * @param ObjectStorage $storage The storage to test.
     * @param int           $count   The expected count.
     * @param array         $tests   An array of arrays as [object, isContained, expectedValue] tests.
     *
     * @return void
     */
    private function assertStorage(ObjectStorage $storage, int $count, array $tests) : void
    {
        $this->assertCount($count, $storage);

        foreach ($tests as list($object, $isContained, $expectedValue)) {
            $this->assertSame($isContained, $storage->has($object));
            $this->assertSame($expectedValue, $storage->get($object));

            $this->assertSame($isContained, isset($storage[$object]));

            try {
                $this->assertSame($expectedValue, $storage[$object]);
            } catch (\UnexpectedValueException $e) {
                if ($expectedValue !== null) {
                    throw $e;
                }
            }
        }
    }

    /**
     * @return ObjectStorage
     */
    public function testEmptyStorage() : ObjectStorage
    {
        $storage = new ObjectStorage();

        $this->assertStorage($storage, 0, [
            [self::$a, false, null],
            [self::$b, false, null]
        ]);

        return $storage;
    }

    /**
     * @depends testEmptyStorage
     *
     * @param ObjectStorage $storage
     *
     * @return ObjectStorage
     */
    public function testSetFirstObject(ObjectStorage $storage) : ObjectStorage
    {
        $storage->set(self::$a, 'x');

        $this->assertStorage($storage, 1, [
            [self::$a, true, 'x'],
            [self::$b, false, null]
        ]);

        return $storage;
    }

    /**
     * @depends testSetFirstObject
     *
     * @param ObjectStorage $storage
     *
     * @return ObjectStorage
     */
    public function testSetSecondObject(ObjectStorage $storage) : ObjectStorage
    {
        $storage[self::$b] = 'y';

        $this->assertStorage($storage, 2, [
            [self::$a, true, 'x'],
            [self::$b, true, 'y']
        ]);

        return $storage;
    }

    /**
     * @depends testSetSecondObject
     *
     * @param ObjectStorage $storage
     *
     * @return ObjectStorage
     */
    public function testRemoveUnknownObjectDoesNothing(ObjectStorage $storage) : ObjectStorage
    {
        $storage->remove(new \StdClass());

        $this->assertStorage($storage, 2, [
            [self::$a, true, 'x'],
            [self::$b, true, 'y']
        ]);

        return $storage;
    }

    /**
     * @depends testRemoveUnknownObjectDoesNothing
     *
     * @param ObjectStorage $storage
     *
     * @return ObjectStorage
     */
    public function testOverwriteFirstObjectWithNull (ObjectStorage $storage) : ObjectStorage
    {
        $storage->set(self::$a, null);

        $this->assertStorage($storage, 2, [
            [self::$a, true, null],
            [self::$b, true, 'y']
        ]);

        return $storage;
    }

    /**
     * @depends testOverwriteFirstObjectWithNull
     *
     * @param ObjectStorage $storage
     *
     * @return ObjectStorage
     */
    public function testRemoveSecondObject (ObjectStorage $storage) : ObjectStorage
    {
        unset($storage[self::$b]);

        $this->assertStorage($storage, 1, [
            [self::$a, true, null],
            [self::$b, false, null]
        ]);

        return $storage;
    }

    /**
     * @depends testRemoveSecondObject
     *
     * @param ObjectStorage $storage
     *
     * @return void
     */
    public function testRemoveFirstObject (ObjectStorage $storage) : void
    {
        $storage->remove(self::$a);

        $this->assertStorage($storage, 0, [
            [self::$a, false, null],
            [self::$b, false, null]
        ]);
    }

    /**
     * @return void
     */
    public function testIterator() : void
    {
        $storage = new ObjectStorage();

        $a = new \StdClass();
        $b = new \StdClass();
        $c = new \StdClass();

        $objects = [$a, $b, $c];
        $values = ['x', 'y', 'z'];

        foreach ($values as $key => $value) {
            $storage->set($objects[$key], $value);
        }

        foreach ($storage as $object => $value) {
            $this->assertInstanceOf(\StdClass::class, $object);

            $key = array_search($object, $objects, true);
            $this->assertNotSame(false, $key);

            $this->assertSame($value, $values[$key]);
        }
    }
}
