<?php

declare(strict_types=1);

namespace Brick\Std;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Traversable;
use UnexpectedValueException;

use function array_values;
use function count;
use function spl_object_id;

/**
 * Provides a map from objects to data.
 *
 * This class is iterable, with objects as keys and data as values.
 *
 * In this respect, this class is different from the SplObjectStorage class,
 * which exhibits a different behaviour due to backwards compatibility reasons.
 *
 * @template K of object
 * @template V
 */
final class ObjectStorage implements Countable, IteratorAggregate, ArrayAccess
{
    /**
     * The objects contained in the storage, as a map of object id to object.
     *
     * @var array<int, K>
     */
    private array $objects = [];

    /**
     * The data in the storage, as a map of object id to datum.
     *
     * @var array<int, V>
     */
    private array $data = [];

    /**
     * Returns whether this storage contains the given object.
     *
     * @param K $object The object to test.
     *
     * @return bool True if this storage contains the object, false otherwise.
     */
    public function has(object $object): bool
    {
        $id = spl_object_id($object);

        return isset($this->objects[$id]);
    }

    /**
     * Returns the data associated to the given object.
     *
     * If the given object is not in the storage, or has no associated data, NULL is returned.
     *
     * @param K $object The object.
     *
     * @return V The stored data.
     */
    public function get(object $object): mixed
    {
        $id = spl_object_id($object);

        return $this->data[$id] ?? null;
    }

    /**
     * Stores an object with associated data.
     *
     * @param K $object The object.
     * @param V $data   The data to store.
     */
    public function set(object $object, mixed $data = null): void
    {
        $id = spl_object_id($object);

        $this->objects[$id] = $object;
        $this->data[$id] = $data;
    }

    /**
     * Removes the given object from this storage, along with associated data.
     *
     * If this storage does not contain the given object, this method does nothing.
     *
     * @param K $object The object to remove.
     */
    public function remove(object $object): void
    {
        $id = spl_object_id($object);

        unset($this->objects[$id]);
        unset($this->data[$id]);
    }

    /**
     * Returns the number of objects in this storage.
     *
     * This method is part of the Countable interface.
     */
    public function count(): int
    {
        return count($this->objects);
    }

    /**
     * Returns the objects contained in this storage.
     *
     * @return V[]
     */
    public function getObjects(): array
    {
        return array_values($this->objects);
    }

    /**
     * Returns an iterator for this storage.
     *
     * This method is part of the IteratorAggregate interface.
     *
     * @return Traversable<K, V>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->objects as $id => $object) {
            yield $object => $this->data[$id];
        }
    }

    /**
     * @param K $object
     *
     * @return V
     *
     * @throws UnexpectedValueException If the object cannot be found.
     */
    public function offsetGet(mixed $object): mixed
    {
        $id = spl_object_id($object);

        if (isset($this->objects[$id])) {
            return $this->data[$id];
        }

        throw new UnexpectedValueException('Object not found.');
    }

    /**
     * @param K $object
     * @param V $value
     */
    public function offsetSet(mixed $object, mixed $value): void
    {
        $id = spl_object_id($object);

        $this->objects[$id] = $object;
        $this->data[$id] = $value;
    }

    /**
     * @param K $object
     */
    public function offsetUnset(mixed $object): void
    {
        $id = spl_object_id($object);

        unset($this->objects[$id]);
        unset($this->data[$id]);
    }

    /**
     * @param K $object
     */
    public function offsetExists(mixed $object): bool
    {
        $id = spl_object_id($object);

        return isset($this->objects[$id]);
    }
}
