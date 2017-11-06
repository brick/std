<?php

namespace Brick\Std;

/**
 * Provides a map from objects to data.
 *
 * This class is iterable, with objects as keys and data as values.
 *
 * In this respect, this class is different from the SplObjectStorage class,
 * which exhibits a different behaviour due to backwards compatibility reasons.
 */
class ObjectStorage implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * The objects contained in the storage, indexed by object hash.
     *
     * @var array<string, object>
     */
    private $objects = [];

    /**
     * The data in the storage, indexed by object hash.
     *
     * @var array
     */
    private $data = [];

    /**
     * Returns whether this storage contains the given object.
     *
     * @param object $object The object to test.
     *
     * @return boolean True if this storage contains the object, false otherwise.
     */
    public function has($object)
    {
        $hash = spl_object_hash($object);

        return isset($this->objects[$hash]);
    }

    /**
     * Returns the data associated to the given object.
     *
     * If the given object is not in the storage, or has no associated data, NULL is returned.
     *
     * @param object $object The object.
     *
     * @return mixed The stored data.
     */
    public function get($object)
    {
        $hash = spl_object_hash($object);

        if (isset($this->data[$hash])) {
            return $this->data[$hash];
        }

        return null;
    }

    /**
     * Stores an object with associated data.
     *
     * @param object $object The object.
     * @param mixed  $data   The data to store.
     *
     * @return void
     */
    public function set($object, $data = null)
    {
        $hash = spl_object_hash($object);

        $this->objects[$hash] = $object;
        $this->data[$hash] = $data;
    }

    /**
     * Removes the given object from this storage, along with associated data.
     *
     * If this storage does not contain the given object, this method does nothing.
     *
     * @param object $object The object to remove.
     *
     * @return void
     */
    public function remove($object)
    {
        $hash = spl_object_hash($object);

        unset($this->objects[$hash]);
        unset($this->data[$hash]);
    }

    /**
     * Returns the number of objects in this storage.
     *
     * This method is part of the Countable interface.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->objects);
    }

    /**
     * Returns the objects contained in this storage.
     *
     * @return array<integer, object>
     */
    public function getObjects()
    {
        return array_values($this->objects);
    }

    /**
     * Returns an iterator for this storage.
     *
     * This method is part of the IteratorAggregate interface.
     *
     * @return \Generator
     */
    public function getIterator()
    {
        foreach ($this->objects as $hash => $object) {
            yield $object => $this->data[$hash];
        }
    }

    /**
     * @param object $object
     *
     * @return mixed
     *
     * @throws \UnexpectedValueException If the object cannot be found.
     */
    public function offsetGet($object)
    {
        $hash = spl_object_hash($object);

        if (isset($this->objects[$hash])) {
            return $this->data[$hash];
        }

        throw new \UnexpectedValueException('Object not found.');
    }

    /**
     * @param object $object
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($object, $value)
    {
        $hash = spl_object_hash($object);

        $this->objects[$hash] = $object;
        $this->data[$hash] = $value;
    }

    /**
     * @param object $object
     *
     * @return void
     */
    public function offsetUnset($object)
    {
        $hash = spl_object_hash($object);

        unset($this->objects[$hash]);
        unset($this->data[$hash]);
    }

    /**
     * @param object $object
     *
     * @return bool
     */
    public function offsetExists($object)
    {
        $hash = spl_object_hash($object);

        return isset($this->objects[$hash]);
    }
}
