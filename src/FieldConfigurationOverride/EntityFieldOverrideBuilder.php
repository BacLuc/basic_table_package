<?php

namespace BaclucC5Crud\FieldConfigurationOverride;

class EntityFieldOverrideBuilder {
    /**
     * @var \ReflectionClass
     */
    private $entityClass;
    /**
     * @var FieldTypeOverrideBuilder
     */
    private $currentFieldOverrideBuilder;

    /**
     * @var array
     */
    private $fieldOverrides = [];

    /**
     * EntityFieldOverrideBuilder constructor.
     *
     * @throws \ReflectionException
     */
    public function __construct(string $entityClass) {
        $this->entityClass = new \ReflectionClass($entityClass);
    }

    /**
     * @throws \ReflectionException if the field does not exist
     */
    public function forField(string $field): EntityFieldOverrideBuilder {
        if (array_key_exists($field, $this->fieldOverrides)) {
            throw new \LogicException('cannot override definitions for one field twice');
        }
        $this->fieldOverrides[$field] = null;
        $this->entityClass->getProperty($field);

        $this->currentFieldOverrideBuilder = new FieldTypeOverrideBuilder($field);

        return $this;
    }

    public function forType(string $interfaceName) {
        $this->currentFieldOverrideBuilder = $this->currentFieldOverrideBuilder->forType($interfaceName);

        return $this;
    }

    public function useFactory(callable $callable) {
        $this->currentFieldOverrideBuilder = $this->currentFieldOverrideBuilder->useFactory($callable);

        return $this;
    }

    public function buildField() {
        $fieldTypeOverride = $this->currentFieldOverrideBuilder->build();
        $this->fieldOverrides[$fieldTypeOverride->getFieldName()] = $fieldTypeOverride;
        $this->currentFieldOverrideBuilder = null;

        return $this;
    }

    public function build() {
        return new EntityFieldOverrides($this->fieldOverrides);
    }
}
