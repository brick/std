<?php

namespace Brick\Std\Tests;

use Brick\Std\ObjectArrayStorage;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class ObjectArrayStorage.
 */
class ObjectArrayStorageTest extends TestCase
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
    public static function setUpBeforeClass()
    {
        self::$a = new \StdClass();
        self::$b = new \StdClass();
    }

    /**
     * @param ObjectArrayStorage $storage  The storage to test.
     * @param integer            $count    The expected count.
     * @param array              $tests    An array of arrays as [object, isContained, expectedValue] tests.
     */
    private function assertStorage(ObjectArrayStorage $storage, $count, array $tests)
    {
        $this->assertCount($count, $storage);

        foreach ($tests as list($object, $isContained, $expectedValue)) {
            $this->assertSame($isContained, $storage->has($object));
            $this->assertSame($expectedValue, $storage->get($object));
        }
    }

    /**
     * @return ObjectArrayStorage
     */
    public function testEmptyStorage()
    {
        $storage = new ObjectArrayStorage();

        $this->assertStorage($storage, 0, [
            [self::$a, false, []],
            [self::$b, false, []]
        ]);

        return $storage;
    }

    /**
     * @depends testEmptyStorage
     *
     * @param ObjectArrayStorage $storage
     *
     * @return ObjectArrayStorage
     */
    public function testAddFirstObject(ObjectArrayStorage $storage)
    {
        $storage->add(self::$a, 'x');

        $this->assertStorage($storage, 1, [
            [self::$a, true, ['x']],
            [self::$b, false, []]
        ]);

        return $storage;
    }

    /**
     * @depends testAddFirstObject
     *
     * @param ObjectArrayStorage $storage
     *
     * @return ObjectArrayStorage
     */
    public function testAddSecondObject(ObjectArrayStorage $storage)
    {
        $storage->add(self::$b, 'y');

        $this->assertStorage($storage, 2, [
            [self::$a, true, ['x']],
            [self::$b, true, ['y']]
        ]);

        return $storage;
    }

    /**
     * @depends testAddSecondObject
     *
     * @param ObjectArrayStorage $storage
     *
     * @return ObjectArrayStorage
     */
    public function testRemoveUnknownObjectDoesNothing(ObjectArrayStorage $storage)
    {
        $storage->remove(new \StdClass());

        $this->assertStorage($storage, 2, [
            [self::$a, true, ['x']],
            [self::$b, true, ['y']]
        ]);

        return $storage;
    }

    /**
     * @depends testRemoveUnknownObjectDoesNothing
     *
     * @param ObjectArrayStorage $storage
     *
     * @return ObjectArrayStorage
     */
    public function testAddValueToFirstObject (ObjectArrayStorage $storage)
    {
        $storage->add(self::$a, 'z');

        $this->assertStorage($storage, 2, [
            [self::$a, true, ['x', 'z']],
            [self::$b, true, ['y']]
        ]);

        return $storage;
    }

    /**
     * @depends testAddValueToFirstObject
     *
     * @param ObjectArrayStorage $storage
     *
     * @return ObjectArrayStorage
     */
    public function testRemoveSecondObject (ObjectArrayStorage $storage)
    {
        $storage->remove(self::$b);

        $this->assertStorage($storage, 1, [
            [self::$a, true, ['x', 'z']],
            [self::$b, false, []]
        ]);

        return $storage;
    }

    /**
     * @depends testRemoveSecondObject
     *
     * @param ObjectArrayStorage $storage
     */
    public function testRemoveFirstObject (ObjectArrayStorage $storage)
    {
        $storage->remove(self::$a);

        $this->assertStorage($storage, 0, [
            [self::$a, false, []],
            [self::$b, false, []]
        ]);
    }

    public function testIterator()
    {
        $storage = new ObjectArrayStorage();

        $a = new \StdClass();
        $b = new \StdClass();
        $c = new \StdClass();

        $objects = [$a, $b, $c];
        $values = [['1', '2'], ['3', '4'], ['5', '6']];

        foreach ($values as $key => $thevalues) {
            foreach ($thevalues as $value) {
                $storage->add($objects[$key], $value);
            }
        }

        foreach ($storage as $object => $thevalues) {
            $this->assertInstanceOf(\StdClass::class, $object);

            $key = array_search($object, $objects, true);
            $this->assertNotSame(false, $key);

            $this->assertSame($thevalues, $values[$key]);
        }
    }
}
