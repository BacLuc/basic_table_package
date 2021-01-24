<?php

namespace BaclucC5Crud\Lib;

use ArrayAccess;
use Ds\Collection;
use Ds\Map;
use Ds\Pair;
use Ds\Sequence;
use Ds\Set;
use http\Exception\InvalidArgumentException;
use OutOfBoundsException;
use OutOfRangeException;
use Stash\Exception\LogicException;
use UnderflowException;

class DuplicateRejectingMap implements \IteratorAggregate, ArrayAccess, Collection {
    /**
     * @var Map
     */
    private $map;

    /**
     * Creates a new instance.
     *
     * @param null|array|\Traversable $values
     */
    public function __construct($values = null) {
        if (func_num_args()) {
            $this->map = new Map();
            $this->putAll($values);
        }
    }

    /**
     * Returns a representation to be used for var_dump and print_r.
     */
    public function __debugInfo() {
        return $this->map->__debugInfo();
    }

    /**
     * Updates all values by applying a callback function to each value.
     *
     * @param callable $callback accepts two arguments: key and value, should
     *                           return what the updated value will be
     */
    public function apply(callable $callback) {
        foreach ($this->map->pairs() as &$pair) {
            $newValue = $callback($pair->key, $pair->value);
            if (null !== $this->lookupValue($newValue)) {
                throw new LogicException("this operation would lead to a duplicate value {$newValue}");
            }
            $pair->value = $newValue;
        }
    }

    public function clear(): void {
        $this->map->clear();
    }

    /**
     * Return the first Pair from the Map.
     *
     * @throws UnderflowException
     */
    public function first(): Pair {
        return $this->map->first();
    }

    /**
     * Return the last Pair from the Map.
     *
     * @throws UnderflowException
     */
    public function last(): Pair {
        return $this->map->last();
    }

    /**
     * Return the pair at a specified position in the Map.
     *
     * @throws OutOfRangeException
     */
    public function skip(int $position): Pair {
        return $this->map->skip($position);
    }

    /**
     * Returns the result of associating all keys of a given traversable object
     * or array with their corresponding values, as well as those of this map.
     *
     * @param array|\Traversable $values
     */
    public function merge($values): DuplicateRejectingMap {
        $merged = new Map($this);
        $merged->putAll($values);

        return new self($values);
    }

    /**
     * Creates a new map containing the pairs of the current instance whose keys
     * are also present in the given map. In other words, returns a copy of the
     * current map with all keys removed that are not also in the other map.
     *
     * @param ArrayAccess $map the other map
     *
     * @return DuplicateRejectingMap A new map containing the pairs of the current instance
     *                               whose keys are also present in the given map. In other
     *                               words, returns a copy of the current map with all keys
     *                               removed that are not also in the other map.
     */
    public function intersect(ArrayAccess $map): DuplicateRejectingMap {
        return $this->filter(function ($key) use ($map) {
            return isset($map[$key]);
        });
    }

    /**
     * Returns the result of removing all keys from the current instance that
     * are present in a given map.
     *
     * @param ArrayAccess $map the map containing the keys to exclude
     *
     * @return DuplicateRejectingMap the result of removing all keys from the current instance
     *                               that are present in a given map
     */
    public function diff(ArrayAccess $map): DuplicateRejectingMap {
        return $this->filter(function ($key) use ($map) {
            return !isset($map[$key]);
        });
    }

    /**
     * Returns whether an association a given key exists.
     *
     * @param mixed $key
     */
    public function hasKey($key): bool {
        return null !== $this->lookupKey($key);
    }

    /**
     * Returns whether an association for a given value exists.
     *
     * @param mixed $value
     */
    public function hasValue($value): bool {
        return null !== $this->lookupValue($value);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int {
        return count($this->pairs());
    }

    /**
     * Returns a new map containing only the values for which a predicate
     * returns true. A boolean test will be used if a predicate is not provided.
     *
     * @param null|callable $callback accepts a key and a value, and returns:
     *                                true : include the value,
     *                                false: skip the value
     */
    public function filter(callable $callback = null): DuplicateRejectingMap {
        $filtered = new self();

        foreach ($this as $key => $value) {
            if ($callback ? $callback($key, $value) : $value) {
                $filtered->put($key, $value);
            }
        }

        return $filtered;
    }

    /**
     * Returns the value associated with a key, or an optional default if the
     * key is not associated with a value.
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @throws OutOfBoundsException if no default was provided and the key is
     *                              not associated with a value
     *
     * @return mixed the associated value or fallback default if provided
     */
    public function get($key, $default = null) {
        if (($pair = $this->lookupKey($key))) {
            return $pair->value;
        }

        // Check if a default was provided.
        if (1 === func_num_args()) {
            throw new OutOfBoundsException();
        }

        return $default;
    }

    /**
     * Returns a set of all the keys in the map.
     */
    public function keys(): Set {
        return $this->map->keys();
    }

    /**
     * Returns a new map using the results of applying a callback to each value.
     *
     * The keys will be equal in both maps.
     *
     * @param callable $callback accepts two arguments: key and value, should
     *                           return what the updated value will be
     */
    public function map(callable $callback): DuplicateRejectingMap {
        return new self($this->map->map($callback));
    }

    /**
     * Returns a sequence of pairs representing all associations.
     */
    public function pairs(): Sequence {
        return $this->map->pairs();
    }

    /**
     * Associates a key with a value, replacing a previous association if there
     * was one.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function put($key, $value) {
        $pair = $this->lookupKey($key);
        $lookedUpValue = $this->lookupValue($value);

        if ($pair || $lookedUpValue) {
            throw new InvalidArgumentException('this map does not support duplicate keys or overwriting keys and also not duplicate values');
        }
        $this->map->put($key, $value);
    }

    /**
     * Creates associations for all keys and corresponding values of either an
     * array or iterable object.
     *
     * @param array|\Traversable $values
     */
    public function putAll($values) {
        foreach ($values as $key => $value) {
            $this->put($key, $value);
        }
    }

    /**
     * Iteratively reduces the map to a single value using a callback.
     *
     * @param callable   $callback accepts the carry, key, and value, and
     *                             returns an updated carry value
     * @param null|mixed $initial  optional initial carry value
     *
     * @return mixed the carry value of the final iteration, or the initial
     *               value if the map was empty
     */
    public function reduce(callable $callback, $initial = null) {
        return $this->map->reduce($callback, $initial);
    }

    /**
     * Removes a key's association from the map and returns the associated value
     * or a provided default if provided.
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @throws OutOfBoundsException if no default was provided and the key is
     *                              not associated with a value
     *
     * @return mixed the associated value or fallback default if provided
     */
    public function remove($key, $default = null) {
        $this->map->remove($key, $default);
    }

    /**
     * Returns a reversed copy of the map.
     *
     * @return DuplicateRejectingMap
     */
    public function reverse() {
        return new self($this->map->reverse());
    }

    /**
     * Returns a reversed copy of the map.
     */
    public function reversed(): DuplicateRejectingMap {
        return $this->reverse();
    }

    /**
     * Returns a sub-sequence of a given length starting at a specified offset.
     *
     * @param int      $offset If the offset is non-negative, the map will
     *                         start at that offset in the map. If offset is
     *                         negative, the map will start that far from the
     *                         end.
     * @param null|int $length If a length is given and is positive, the
     *                         resulting set will have up to that many pairs in
     *                         it. If the requested length results in an
     *                         overflow, only pairs up to the end of the map
     *                         will be included.
     *
     *                         If a length is given and is negative, the map
     *                         will stop that many pairs from the end.
     *
     *                        If a length is not provided, the resulting map
     *                        will contains all pairs between the offset and
     *                        the end of the map.
     */
    public function slice(int $offset, int $length = null): DuplicateRejectingMap {
        return new self($this->map->slice($offset, $length));
    }

    /**
     * Sorts the map in-place, based on an optional callable comparator.
     *
     * The map will be sorted by value.
     *
     * @param null|callable $comparator accepts two values to be compared
     */
    public function sort(callable $comparator = null) {
        $this->map->sort($comparator);
    }

    /**
     * Sorts the map in-place, based on an optional callable comparator.
     *
     * The map will be sorted by key.
     *
     * @param null|callable $comparator accepts two keys to be compared
     */
    public function ksort(callable $comparator = null) {
        $this->map->ksort($comparator);
    }

    /**
     * Returns the sum of all values in the map.
     *
     * @return float|int the sum of all the values in the map
     */
    public function sum() {
        return $this->map->sum();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array {
        return $this->map->toArray();
    }

    /**
     * Returns a sequence of all the associated values in the Map.
     */
    public function values(): Sequence {
        return $this->map->values();
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator() {
        return $this->map->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value) {
        $this->put($offset, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @throws OutOfBoundsException
     */
    public function &offsetGet($offset) {
        return $this->map->offsetGet($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset) {
        return $this->map->offsetUnset($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset) {
        return $this->map->offsetExists($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function copy(): Collection {
        return new self($this->map->copy()->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool {
        return $this->map->isEmpty();
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize() {
        return $this->map->jsonSerialize();
    }

    /**
     * Attempts to look up a key in the table.
     *
     * @param $key
     *
     * @return null|Pair
     */
    private function lookupKey($key) {
        return $this->map->hasKey($key) ? $this->map[$key] : null;
    }

    /**
     * Attempts to look up a key in the table.
     *
     * @param $value
     *
     * @return null|Pair
     */
    private function lookupValue($value) {
        return $this->map->hasValue($value) ? $this->map->filter(function ($__, $mapValue) use ($value) {
            return $value === $mapValue;
        })->first() : null;
    }
}
