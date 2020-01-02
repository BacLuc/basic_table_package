<?php


namespace BasicTablePackage\Controller\Validation;


use BasicTablePackage\FieldConfigurationOverride\EntityFieldOverrides;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldType;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypeReader;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypes;
use BasicTablePackage\FieldTypeDetermination\ReferencingPersistenceFieldType;
use function BasicTablePackage\Lib\collect as collect;

class ValidationConfigurationFactory
{
    /**
     * @var PersistenceFieldTypeReader
     */
    private $persistenceFieldTypeReader;
    /**
     * @var EntityFieldOverrides
     */
    private $entityFieldOverrides;

    /**
     * @param PersistenceFieldTypeReader $persistenceFieldTypeReader
     * @param EntityFieldOverrides $entityFieldOverrides
     */
    public function __construct(
        PersistenceFieldTypeReader $persistenceFieldTypeReader,
        EntityFieldOverrides $entityFieldOverrides
    ) {
        $this->persistenceFieldTypeReader = $persistenceFieldTypeReader;
        $this->entityFieldOverrides = $entityFieldOverrides;
    }

    public function createConfiguration(): ValidationConfiguration
    {
        $fieldTypes =
            collect($this->persistenceFieldTypeReader->getPersistenceFieldTypes())
                ->map(function ($persistenceFieldType, $key) {
                    return self::createFieldTypeOf($persistenceFieldType,
                        $key);
                });
        return new ValidationConfiguration($fieldTypes->toArray());
    }

    private function createFieldTypeOf(PersistenceFieldType $persistenceFieldType, string $key)
    {
        if (isset($this->entityFieldOverrides[$key]) &&
            isset($this->entityFieldOverrides[$key][FieldValidator::class])) {
            return $this->entityFieldOverrides[$key][FieldValidator::class]($key);
        }
        switch ($persistenceFieldType->getType()) {
            case PersistenceFieldTypes::TEXT:
            case PersistenceFieldTypes::STRING:
                return new TextFieldValidator($key);
            case PersistenceFieldTypes::INTEGER:
                return new IntegerFieldValidator($key);
            case PersistenceFieldTypes::DATE:
            case PersistenceFieldTypes::DATETIME:
                return new DateValidator($key);
            case PersistenceFieldTypes::MANY_TO_ONE:
                /** @var ReferencingPersistenceFieldType $persistenceFieldType */
                return new DropdownFieldValidator($key, $persistenceFieldType->getValueSupplier());
            case PersistenceFieldTypes::MANY_TO_MANY:
                /** @var ReferencingPersistenceFieldType $persistenceFieldType */
                return new SelectMultipleFieldValidator($key, $persistenceFieldType->getValueSupplier());
            default:
                return null;
        }
    }
}