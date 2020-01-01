<?php


namespace BasicTablePackage\FieldConfigurationOverride;


use BasicTablePackage\Lib\ImmutableArrayAccessTrait;

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