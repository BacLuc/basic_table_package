<?php


namespace BasicTablePackage\FieldConfigurationOverride;


use ArrayAccess;
use BasicTablePackage\Lib\ImmutableArrayAccessTrait;

class FieldTypeOverride implements ArrayAccess
{
    use ImmutableArrayAccessTrait;
    /**
     * @var string
     */
    private $fieldName;

    /**
     * FieldTypeOverride constructor.
     * @param string $fieldName
     * @param array $overrides
     * @internal
     */
    public function __construct(string $fieldName, array $overrides)
    {
        $this->fieldName = $fieldName;
        $this->initialize($overrides);
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }


}