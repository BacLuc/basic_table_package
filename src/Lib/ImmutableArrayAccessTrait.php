<?php


namespace BasicTablePackage\Lib;


trait ImmutableArrayAccessTrait
{
    /**
     * @var array
     */
    private $values;

    /**
     * FieldTypeOverride constructor.
     * private, do not use directly
     * @param array $overrides
     */
    function initialize(array $values)
    {
        $this->values = $values;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->values);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException("this array is immutable, cannot set value");
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException("this array is immutable, cannot set value");
    }
}