<?php


namespace BasicTablePackage\Lib;


use ArrayAccess;
use BadMethodCallException;

interface ImmutableArrayAccess extends ArrayAccess
{
    /**
     * @throws BadMethodCallException
     */
    public function offsetSet($offset, $value);

    /**
     * @throws BadMethodCallException
     */
    public function offsetUnset($offset);
}