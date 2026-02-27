<?php

declare(strict_types=1);

namespace Brick\Std\Tests;

use Brick\Std\ObjectArrayStorage;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use stdClass;

use function array_search;

/**
 * Unit tests for class ObjectArrayStorage.
 */
class ObjectArrayStorageTest extends TestCase
{
    private static object $a;

    private static object $b;

    public static function setUpBeforeClass(): void
    {
        self::$a = new stdClass();
        self::$b = new stdClass();
    }

    public function testEmptyStorage(): ObjectArrayStorage
    {
        $storage = new ObjectArrayStorage();

        $this->assertStorage($storage, 0, [
            [self::$a, false, []],
            [self::$b, false, []],
        ]);

        return $storage;
    }

    #[Depends('testEmptyStorage')]
    public function testAddFirstObject(ObjectArrayStorage $storage): ObjectArrayStorage
    {
        $storage->add(self::$a, 'x');

        $this->assertStorage($storage, 1, [
            [self::$a, true, ['x']],
            [self::$b, false, []],
        ]);

        return $storage;
    }

    #[Depends('testAddFirstObject')]
    public function testAddSecondObject(ObjectArrayStorage $storage): ObjectArrayStorage
    {
        $storage->add(self::$b, 'y');

        $this->assertStorage($storage, 2, [
            [self::$a, true, ['x']],
            [self::$b, true, ['y']],
        ]);

        return $storage;
    }

    #[Depends('testAddSecondObject')]
    public function testRemoveUnknownObjectDoesNothing(ObjectArrayStorage $storage): ObjectArrayStorage
    {
        $storage->remove(new stdClass());

        $this->assertStorage($storage, 2, [
            [self::$a, true, ['x']],
            [self::$b, true, ['y']],
        ]);

        return $storage;
    }

    #[Depends('testRemoveUnknownObjectDoesNothing')]
    public function testAddValueToFirstObject(ObjectArrayStorage $storage): ObjectArrayStorage
    {
        $storage->add(self::$a, 'z');

        $this->assertStorage($storage, 2, [
            [self::$a, true, ['x', 'z']],
            [self::$b, true, ['y']],
        ]);

        return $storage;
    }

    #[Depends('testAddValueToFirstObject')]
    public function testRemoveSecondObject(ObjectArrayStorage $storage): ObjectArrayStorage
    {
        $storage->remove(self::$b);

        $this->assertStorage($storage, 1, [
            [self::$a, true, ['x', 'z']],
            [self::$b, false, []],
        ]);

        return $storage;
    }

    #[Depends('testRemoveSecondObject')]
    public function testRemoveFirstObject(ObjectArrayStorage $storage): void
    {
        $storage->remove(self::$a);

        $this->assertStorage($storage, 0, [
            [self::$a, false, []],
            [self::$b, false, []],
        ]);
    }

    public function testIterator(): void
    {
        $storage = new ObjectArrayStorage();

        $a = new stdClass();
        $b = new stdClass();
        $c = new stdClass();

        $objects = [$a, $b, $c];
        $values = [['1', '2'], ['3', '4'], ['5', '6']];

        foreach ($values as $key => $thevalues) {
            foreach ($thevalues as $value) {
                $storage->add($objects[$key], $value);
            }
        }

        foreach ($storage as $object => $thevalues) {
            self::assertInstanceOf(stdClass::class, $object);

            $key = array_search($object, $objects, true);
            self::assertNotSame(false, $key);

            self::assertSame($thevalues, $values[$key]);
        }
    }

    /**
     * @param ObjectArrayStorage $storage The storage to test.
     * @param int                $count   The expected count.
     * @param array              $tests   An array of arrays as [object, isContained, expectedValue] tests.
     */
    private function assertStorage(ObjectArrayStorage $storage, int $count, array $tests): void
    {
        self::assertCount($count, $storage);

        foreach ($tests as [$object, $isContained, $expectedValue]) {
            self::assertSame($isContained, $storage->has($object));
            self::assertSame($expectedValue, $storage->get($object));
        }
    }
}
