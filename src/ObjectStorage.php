<?php

declare(strict_types=1);

namespace Brick\Std;

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
final class ObjectStorage implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * The objects contained in the storage, as a map of object id to object.
     *
     * @psalm-var array<int, K>
     *
     * @var array<int, object>
     */
    private array $objects = [];

    /**
     * The data in the storage, as a map of object id to datum.
     *
     * @psalm-var array<int, V>
     *
     * @var array<int, mixed>
     */
    private array $data = [];

    /**
     * Returns whether this storage contains the given object.
     *
     * @psalm-param K $object
     *
     * @param object $object The object to test.
     *
     * @return bool True if this storage contains the object, false otherwise.
     */
    public function has(object $object) : bool
    {
        $id = spl_object_id($object);

        return isset($this->objects[$id]);
    }

    /**
     * Returns the data associated to the given object.
     *
     * If the given object is not in the storage, or has no associated data, NULL is returned.
     *
     * @psalm-param K $object
     * @psalm-return V
     *
     * @param object $object The object.
     *
     * @return mixed The stored data.
     */
    public function get(object $object)
    {
        $id = spl_object_id($object);

        return $this->data[$id] ?? null;
    }

    /**
     * Stores an object with associated data.
     *
     * @psalm-param K $object
     * @psalm-param V $data
     *
     * @param object $object The object.
     * @param mixed  $data   The data to store.
     *
     * @return void
     */
    public function set(object $object, $data = null) : void
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
     * @psalm-param K $object
     *
     * @param object $object The object to remove.
     *
     * @return void
     */
    public function remove(object $object) : void
    {
        $id = spl_object_id($object);

        unset($this->objects[$id]);
        unset($this->data[$id]);
    }

    /**
     * Returns the number of objects in this storage.
     *
     * This method is part of the Countable interface.
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->objects);
    }

    /**
     * Returns the objects contained in this storage.
     *
     * @psalm-return V[]
     *
     * @return object[]
     */
    public function getObjects() : array
    {
        return array_values($this->objects);
    }

    /**
     * Returns an iterator for this storage.
     *
     * This method is part of the IteratorAggregate interface.
     *
     * @psalm-return \Traversable<K, V>
     *
     * @return \Traversable
     */
    public function getIterator() : \Traversable
    {
        foreach ($this->objects as $id => $object) {
            yield $object => $this->data[$id];
        }
    }

    /**
     * @psalm-param K $object
     * @psalm-return V
     *
     * @param object $object
     *
     * @return mixed
     *
     * @throws \UnexpectedValueException If the object cannot be found.
     */
    public function offsetGet(mixed $object): mixed
    {
        $id = spl_object_id($object);

        if (isset($this->objects[$id])) {
            return $this->data[$id];
        }

        throw new \UnexpectedValueException('Object not found.');
    }

    /**
     * @psalm-param K $object
     * @psalm-param V $value
     *
     * @param object $object
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet(mixed $object, mixed $value) : void
    {
        $id = spl_object_id($object);

        $this->objects[$id] = $object;
        $this->data[$id] = $value;
    }

    /**
     * @psalm-param K $object
     *
     * @param object $object
     *
     * @return void
     */
    public function offsetUnset(mixed $object) : void
    {
        $id = spl_object_id($object);

        unset($this->objects[$id]);
        unset($this->data[$id]);
    }

    /**
     * @psalm-param K $object
     *
     * @param object $object
     *
     * @return bool
     */
    public function offsetExists(mixed $object) : bool
    {
        $id = spl_object_id($object);

        return isset($this->objects[$id]);
    }
}
