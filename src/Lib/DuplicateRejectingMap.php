<?php


namespace BasicTablePackage\Lib;


use Ds\Collection;
use Ds\Hashable;
use Ds\Map;
use Ds\Pair;
use Ds\Sequence;
use Ds\Set;
use Ds\Vector;
use http\Exception\InvalidArgumentException;

class DuplicateRejectingMap implements \IteratorAggregate, \ArrayAccess, Collection
{

    /**
     * @var Map
     */
    private $map;

    /**
     * Creates a new instance.
     *
     * @param array|\Traversable|null $values
     */
    public function __construct($values = null)
    {
        if (func_num_args()) {
            $this->map = new Map();
            $this->putAll($values);
        }
    }

    /**
     * Updates all values by applying a callback function to each value.
     *
     * @param callable $callback Accepts two arguments: key and value, should
     *                           return what the updated value will be.
     */
    public function apply(callable $callback)
    {
        foreach ($this->pairs as &$pair) {
            $pair->value = $callback($pair->key, $pair->value);
        }
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->pairs = [];
        $this->capacity = self::MIN_CAPACITY;
    }

    /**
     * Return the first Pair from the Map
     *
     * @return Pair
     *
     * @throws UnderflowException
     */
    public function first(): Pair
    {
        if ($this->isEmpty()) {
            throw new UnderflowException();
        }

        return $this->pairs[0];
    }

    /**
     * Return the last Pair from the Map
     *
     * @return Pair
     *
     * @throws UnderflowException
     */
    public function last(): Pair
    {
        if ($this->isEmpty()) {
            throw new UnderflowException();
        }

        return $this->pairs[count($this->pairs) - 1];
    }

    /**
     * Return the pair at a specified position in the Map
     *
     * @param int $position
     *
     * @return Pair
     *
     * @throws OutOfRangeException
     */
    public function skip(int $position): Pair
    {
        if ($position < 0 || $position >= count($this->pairs)) {
            throw new OutOfRangeException();
        }

        return $this->pairs[$position]->copy();
    }

    /**
     * Returns the result of associating all keys of a given traversable object
     * or array with their corresponding values, as well as those of this map.
     *
     * @param array|\Traversable $values
     *
     * @return Map
     */
    public function merge($values): Map
    {
        $merged = new self($this);
        $merged->putAll($values);
        return $merged;
    }

    /**
     * Creates a new map containing the pairs of the current instance whose keys
     * are also present in the given map. In other words, returns a copy of the
     * current map with all keys removed that are not also in the other map.
     *
     * @param Map $map The other map.
     *
     * @return Map A new map containing the pairs of the current instance
     *                 whose keys are also present in the given map. In other
     *                 words, returns a copy of the current map with all keys
     *                 removed that are not also in the other map.
     */
    public function intersect(Map $map): Map
    {
        return $this->filter(function ($key) use ($map) {
            return $map->hasKey($key);
        });
    }

    /**
     * Returns the result of removing all keys from the current instance that
     * are present in a given map.
     *
     * @param Map $map The map containing the keys to exclude.
     *
     * @return Map The result of removing all keys from the current instance
     *                 that are present in a given map.
     */
    public function diff(Map $map): Map
    {
        return $this->filter(function ($key) use ($map) {
            return !$map->hasKey($key);
        });
    }

    /**
     * Determines whether two keys are equal.
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return bool
     */
    private function keysAreEqual($a, $b): bool
    {
        if (is_object($a) && $a instanceof Hashable) {
            return get_class($a) === get_class($b) && $a->equals($b);
        }

        return $a === $b;
    }

    /**
     * Attempts to look up a key in the table.
     *
     * @param $key
     *
     * @return Pair|null
     */
    private function lookupKey($key)
    {
        return $this->map->hasKey($key) ? $this->map[$key] : null;
    }

    /**
     * Attempts to look up a key in the table.
     *
     * @param $value
     *
     * @return Pair|null
     */
    private function lookupValue($value)
    {
        return $this->map->hasValue($value) ? $this->map->filter(function ($__, $mapValue) use ($value) {
            return $value === $mapValue;
        })->first() : null;
    }

    /**
     * Returns whether an association a given key exists.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function hasKey($key): bool
    {
        return $this->lookupKey($key) !== null;
    }

    /**
     * Returns whether an association for a given value exists.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function hasValue($value): bool
    {
        return $this->lookupValue($value) !== null;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->pairs);
    }

    /**
     * Returns a new map containing only the values for which a predicate
     * returns true. A boolean test will be used if a predicate is not provided.
     *
     * @param callable|null $callback Accepts a key and a value, and returns:
     *                                true : include the value,
     *                                false: skip the value.
     *
     * @return Map
     */
    public function filter(callable $callback = null): Map
    {
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
     * @return mixed The associated value or fallback default if provided.
     *
     * @throws OutOfBoundsException if no default was provided and the key is
     *                               not associated with a value.
     */
    public function get($key, $default = null)
    {
        if (($pair = $this->lookupKey($key))) {
            return $pair->value;
        }

        // Check if a default was provided.
        if (func_num_args() === 1) {
            throw new OutOfBoundsException();
        }

        return $default;
    }

    /**
     * Returns a set of all the keys in the map.
     *
     * @return Set
     */
    public function keys(): Set
    {
        $key = function ($pair) {
            return $pair->key;
        };

        return new Set(array_map($key, $this->pairs));
    }

    /**
     * Returns a new map using the results of applying a callback to each value.
     *
     * The keys will be equal in both maps.
     *
     * @param callable $callback Accepts two arguments: key and value, should
     *                           return what the updated value will be.
     *
     * @return Map
     */
    public function map(callable $callback): Map
    {
        $apply = function ($pair) use ($callback) {
            return $callback($pair->key, $pair->value);
        };

        return new self(array_map($apply, $this->pairs));
    }

    /**
     * Returns a sequence of pairs representing all associations.
     *
     * @return Sequence
     */
    public function pairs(): Sequence
    {
        $copy = function ($pair) {
            return $pair->copy();
        };

        return new Vector(array_map($copy, $this->pairs));
    }

    /**
     * Associates a key with a value, replacing a previous association if there
     * was one.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function put($key, $value)
    {
        $pair = $this->lookupKey($key);
        $lookedUpValue = $this->lookupValue($value);

        if ($pair || $lookedUpValue) {
            throw new InvalidArgumentException("this map does not support duplicate keys or overwriting keys and also not duplicate values");
        } else {
            $this->map->put($key, $value);
        }
    }

    /**
     * Creates associations for all keys and corresponding values of either an
     * array or iterable object.
     *
     * @param \Traversable|array $values
     */
    public function putAll($values)
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value);
        }
    }

    /**
     * Iteratively reduces the map to a single value using a callback.
     *
     * @param callable $callback Accepts the carry, key, and value, and
     *                           returns an updated carry value.
     *
     * @param mixed|null $initial Optional initial carry value.
     *
     * @return mixed The carry value of the final iteration, or the initial
     *               value if the map was empty.
     */
    public function reduce(callable $callback, $initial = null)
    {
        return $this->map->reduce($callback, $initial);
    }

    /**
     * Removes a key's association from the map and returns the associated value
     * or a provided default if provided.
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed The associated value or fallback default if provided.
     *
     * @throws \OutOfBoundsException if no default was provided and the key is
     *                               not associated with a value.
     */
    public function remove($key, $default = null)
    {
        $this->map->remove($key, $default);
    }

    /**
     * Returns a reversed copy of the map.
     *
     * @return Map
     */
    public function reverse()
    {
        return $this->map->reverse();
    }

    /**
     * Returns a reversed copy of the map.
     *
     * @return Map
     */
    public function reversed(): Map
    {
        return $this->map->reversed();
    }

    /**
     * Returns a sub-sequence of a given length starting at a specified offset.
     *
     * @param int $offset If the offset is non-negative, the map will
     *                         start at that offset in the map. If offset is
     *                         negative, the map will start that far from the
     *                         end.
     *
     * @param int|null $length If a length is given and is positive, the
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
     *
     * @return Map
     */
    public function slice(int $offset, int $length = null): Map
    {
        return $this->map->slice($offset, $length);
    }

    /**
     * Sorts the map in-place, based on an optional callable comparator.
     *
     * The map will be sorted by value.
     *
     * @param callable|null $comparator Accepts two values to be compared.
     */
    public function sort(callable $comparator = null)
    {
        $this->map->sort($comparator);
    }

    /**
     * Returns a sorted copy of the map, based on an optional callable
     * comparator. The map will be sorted by value.
     *
     * @param callable|null $comparator Accepts two values to be compared.
     *
     * @return Map
     */
    public function sorted(callable $comparator = null): Map
    {
        return $this->map->sorted($comparator);
    }

    /**
     * Sorts the map in-place, based on an optional callable comparator.
     *
     * The map will be sorted by key.
     *
     * @param callable|null $comparator Accepts two keys to be compared.
     */
    public function ksort(callable $comparator = null)
    {
        $this->map->ksort($comparator);
    }

    /**
     * Returns a sorted copy of the map, based on an optional callable
     * comparator. The map will be sorted by key.
     *
     * @param callable|null $comparator Accepts two keys to be compared.
     *
     * @return Map
     */
    public function ksorted(callable $comparator = null): Map
    {
        return $this->map->ksorted($comparator);
    }

    /**
     * Returns the sum of all values in the map.
     *
     * @return int|float The sum of all the values in the map.
     */
    public function sum()
    {
        return $this->map->sum();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->map->toArray();
    }

    /**
     * Returns a sequence of all the associated values in the Map.
     *
     * @return Sequence
     */
    public function values(): Sequence
    {
        return $this->map->values();
    }

    /**
     * Creates a new map that contains the pairs of the current instance as well
     * as the pairs of another map.
     *
     * @param Map $map The other map, to combine with the current instance.
     *
     * @return Map A new map containing all the pairs of the current
     *                 instance as well as another map.
     */
    public function union(Map $map): Map
    {
        return $this->map->union($map);
    }

    /**
     * Creates a new map using keys of either the current instance or of another
     * map, but not of both.
     *
     * @param Map $map
     *
     * @return Map A new map containing keys in the current instance as well
     *                 as another map, but not in both.
     */
    public function xor(Map $map): Map
    {
        return $this->map->xor($map);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->map->getIterator();
    }

    /**
     * Returns a representation to be used for var_dump and print_r.
     */
    public function __debugInfo()
    {
        return $this->map->__debugInfo();
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->put($offset, $value);
    }

    /**
     * @inheritdoc
     *
     * @throws OutOfBoundsException
     */
    public function &offsetGet($offset)
    {
        return $this->map->offsetGet($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        return $this->map->offsetUnset($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return $this->map->offsetExists($offset);
    }

    /**
     * @inheritDoc
     */
    function copy(): Collection
    {
        return new self($this->map->copy()->toArray());
    }

    /**
     * @inheritDoc
     */
    function isEmpty(): bool
    {
        return $this->map->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->map->jsonSerialize();
    }
}