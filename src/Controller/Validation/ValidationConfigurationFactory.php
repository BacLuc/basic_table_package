<?php

namespace BaclucC5Crud\Controller\Validation;

use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrides;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldType;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypeReader;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypes;
use BaclucC5Crud\FieldTypeDetermination\ReferencingPersistenceFieldType;
use function BaclucC5Crud\Lib\collect as collect;

class ValidationConfigurationFactory {
    /**
     * @var PersistenceFieldTypeReader
     */
    private $persistenceFieldTypeReader;
    /**
     * @var EntityFieldOverrides
     */
    private $entityFieldOverrides;

    public function __construct(
        PersistenceFieldTypeReader $persistenceFieldTypeReader,
        EntityFieldOverrides $entityFieldOverrides
    ) {
        $this->persistenceFieldTypeReader = $persistenceFieldTypeReader;
        $this->entityFieldOverrides = $entityFieldOverrides;
    }

    public function createConfiguration(): ValidationConfiguration {
        $fieldTypes =
            collect($this->persistenceFieldTypeReader->getPersistenceFieldTypes())
                ->map(function ($persistenceFieldType, $key) {
                    return self::createFieldTypeOf(
                        $persistenceFieldType,
                        $key
                    );
                })->filter(function ($value) {
                    return null != $value;
                });

        return new ValidationConfiguration($fieldTypes->toArray());
    }

    private function createFieldTypeOf(PersistenceFieldType $persistenceFieldType, string $key) {
        if (isset($this->entityFieldOverrides[$key], $this->entityFieldOverrides[$key][FieldValidator::class])
            ) {
            return $this->entityFieldOverrides[$key][FieldValidator::class]($key);
        }
        $validators = [];
        $validators[] = $this->getValidatorForType($persistenceFieldType, $key);

        if (!$persistenceFieldType->isNullable()) {
            $validators[] = new NotNullValidator($key);
        }

        return new CombinedValidator($key, $validators);
    }

    /**
     * @return null|DateValidator|DropdownFieldValidator|IntegerFieldValidator|SelectMultipleFieldValidator|TextFieldValidator
     */
    private function getValidatorForType(PersistenceFieldType $persistenceFieldType, string $key) {
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
                // @var ReferencingPersistenceFieldType $persistenceFieldType
                return new DropdownFieldValidator($key, $persistenceFieldType->getValueSupplier());

            case PersistenceFieldTypes::MANY_TO_MANY:
                // @var ReferencingPersistenceFieldType $persistenceFieldType
                return new SelectMultipleFieldValidator($key, $persistenceFieldType->getValueSupplier());

            default:
                return null;
        }
    }
}
