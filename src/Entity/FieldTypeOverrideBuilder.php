<?php


namespace BasicTablePackage\Entity;


use LogicException;

class FieldTypeOverrideBuilder
{
    /**
     * @var string
     */
    private $fieldName;
    private $overrides = [];
    private $currentOverride;

    /**
     * FieldTypeOverrideBuilder constructor.
     * @param string $fieldName
     */
    public function __construct(string $fieldName)
    {
        $this->fieldName = $fieldName;
    }


    public function forType(string $interfaceName): FieldTypeOverrideBuilder
    {
        if (!interface_exists($interfaceName)) {
            throw new \InvalidArgumentException("interface $interfaceName does not exist, please use an existing interface name");
        }
        if (array_key_exists($interfaceName, $this->overrides)) {
            throw new LogicException("cannot define override for same interface twice. Override for interface $interfaceName is already defined");
        }

        $this->currentOverride = $interfaceName;
        $this->overrides[$interfaceName] = null;
        return $this;
    }

    public function useFactory(callable $factory): FieldTypeOverrideBuilder
    {
        if ($this->currentOverride == null) {
            throw new LogicException("you need to call forType before you can call useFactory");
        }
        $this->overrides[$this->currentOverride] = $factory;
        $this->currentOverride = null;
        return $this;
    }

    public function build()
    {
        return new FieldTypeOverride($this->fieldName, $this->overrides);
    }
}