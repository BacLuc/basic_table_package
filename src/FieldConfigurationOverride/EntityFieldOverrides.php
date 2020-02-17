<?php


namespace BaclucC5Crud\FieldConfigurationOverride;


use BaclucC5Crud\Lib\ImmutableArrayAccessTrait;

class EntityFieldOverrides implements \ArrayAccess
{
    use ImmutableArrayAccessTrait;


    /**
     * @param array $overrides
     * @internal
     * EntityFieldOverrides constructor.
     */
    public function __construct(array $overrides)
    {
        $this->initialize($overrides);
    }
}