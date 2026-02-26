<?php

declare(strict_types=1);

namespace Brick\Std\Tests;

use Brick\Std\ObjectStorage;

use PHPUnit\Framework\Attributes\Depends;
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
        self::$a = new \stdClass();
        self::$b = new \stdClass();
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

        foreach ($tests as [$object, $isContained, $expectedValue]) {
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
     * @param ObjectStorage $storage
     *
     * @return ObjectStorage
     */
    #[Depends('testEmptyStorage')]
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
     * @param ObjectStorage $storage
     *
     * @return ObjectStorage
     */
    #[Depends('testSetFirstObject')]
    public function testSetSecondObject(ObjectStorage $storage) : ObjectStorage
    {
        $storage[self::$b] = 'y';

        $this->assertStorage($storage, 2, [
            [self::$a, true, 'x'],
            [self::$b, true, 'y']
        ]);

        return $storage;
    }

    public function testGetObjects()
    {
        $storage = new ObjectStorage();

        $a = new \stdClass();
        $b = new \stdClass();
        $c = new \stdClass();

        $objects = [$a, $b, $c];
        foreach($objects as $value) {
            $storage->set($value, null);
        }

        $result = $storage->getObjects();

        $this->assertSame($objects, $result);
    }

    /**
     * @param ObjectStorage $storage
     *
     * @return ObjectStorage
     */
    #[Depends('testSetSecondObject')]
    public function testRemoveUnknownObjectDoesNothing(ObjectStorage $storage) : ObjectStorage
    {
        $storage->remove(new \stdClass());

        $this->assertStorage($storage, 2, [
            [self::$a, true, 'x'],
            [self::$b, true, 'y']
        ]);

        return $storage;
    }

    /**
     * @param ObjectStorage $storage
     *
     * @return ObjectStorage
     */
    #[Depends('testRemoveUnknownObjectDoesNothing')]
    public function testOverwriteFirstObjectWithNull(ObjectStorage $storage) : ObjectStorage
    {
        $storage->set(self::$a, null);

        $this->assertStorage($storage, 2, [
            [self::$a, true, null],
            [self::$b, true, 'y']
        ]);

        return $storage;
    }

    /**
     * @param ObjectStorage $storage
     *
     * @return ObjectStorage
     */
    #[Depends('testOverwriteFirstObjectWithNull')]
    public function testRemoveSecondObject(ObjectStorage $storage) : ObjectStorage
    {
        unset($storage[self::$b]);

        $this->assertStorage($storage, 1, [
            [self::$a, true, null],
            [self::$b, false, null]
        ]);

        return $storage;
    }

    /**
     * @param ObjectStorage $storage
     *
     * @return void
     */
    #[Depends('testRemoveSecondObject')]
    public function testRemoveFirstObject(ObjectStorage $storage) : void
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

        $a = new \stdClass();
        $b = new \stdClass();
        $c = new \stdClass();

        $objects = [$a, $b, $c];
        $values = ['x', 'y', 'z'];

        foreach ($values as $key => $value) {
            $storage->set($objects[$key], $value);
        }

        foreach ($storage as $object => $value) {
            $this->assertInstanceOf(\stdClass::class, $object);

            $key = array_search($object, $objects, true);
            $this->assertNotSame(false, $key);

            $this->assertSame($value, $values[$key]);
        }
    }
}
