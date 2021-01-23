<?php

namespace BaclucC5Crud\Lib;

trait IteratorTrait {
    /**
     * @var string[]
     */
    private $values;

    /**
     * Row constructor.
     *
     * @param string[] $values
     */
    public function initialize(array $values) {
        $this->values = $values;
    }

    /**
     * Return the current element.
     *
     * @see https://php.net/manual/en/iterator.current.php
     *
     * @return mixed can return any type
     *
     * @since 5.0.0
     */
    public function current() {
        return current($this->values);
    }

    /**
     * Move forward to next element.
     *
     * @see https://php.net/manual/en/iterator.next.php
     * @since 5.0.0
     */
    public function next() {
        next($this->values);
    }

    /**
     * Return the key of the current element.
     *
     * @see https://php.net/manual/en/iterator.key.php
     *
     * @return mixed scalar on success, or null on failure
     *
     * @since 5.0.0
     */
    public function key() {
        return key($this->values);
    }

    /**
     * Checks if current position is valid.
     *
     * @see https://php.net/manual/en/iterator.valid.php
     *
     * @return bool The return value will be casted to boolean and then evaluated.
     *              Returns true on success or false on failure.
     *
     * @since 5.0.0
     */
    public function valid() {
        return null !== key($this->values);
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @see https://php.net/manual/en/iterator.rewind.php
     * @since 5.0.0
     */
    public function rewind() {
        reset($this->values);
    }
}
