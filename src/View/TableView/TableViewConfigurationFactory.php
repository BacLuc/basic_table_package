<?php


namespace BaclucC5Crud\View\TableView;


use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrides;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldType;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypeReader;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypes;
use BaclucC5Crud\FieldTypeDetermination\ReferencingPersistenceFieldType;
use function BaclucC5Crud\Lib\collect as collect;

class TableViewConfigurationFactory
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

    public function createConfiguration(): TableViewFieldConfiguration
    {
        $persistenceFieldTypes = $this->persistenceFieldTypeReader->getPersistenceFieldTypes();
        $fieldTypes =
            collect($persistenceFieldTypes)
                ->map(function ($persistenceFieldType) {
                    return self::createFieldTypeOf($persistenceFieldType);
                });
        return new TableViewFieldConfiguration($fieldTypes->toArray());
    }

    private function createFieldTypeOf(PersistenceFieldType $persistenceFieldType)
    {
        switch ($persistenceFieldType->getType()) {
            case PersistenceFieldTypes::STRING:
            case PersistenceFieldTypes::INTEGER:
            case PersistenceFieldTypes::TEXT:
                return function ($value, $key) {
                    return $this->checkFieldOverride($value, $key) ?: new TextField($value);
                };
            case PersistenceFieldTypes::DATE:
                return function ($value, $key) {
                    return $this->checkFieldOverride($value, $key) ?: new DateField($value);
                };
            case PersistenceFieldTypes::DATETIME:
                return function ($value, $key) {
                    return $this->checkFieldOverride($value, $key) ?: new DateTimeField($value);
                };
            case PersistenceFieldTypes::MANY_TO_ONE:
                return function ($value, $key) use ($persistenceFieldType) {
                    /** @var ReferencingPersistenceFieldType $persistenceFieldType */
                    return $this->checkFieldOverride($value, $key) ?:
                        new DropdownField($value, $persistenceFieldType->getValueSupplier());
                };
            case PersistenceFieldTypes::MANY_TO_MANY:
                return function ($value, $key) use ($persistenceFieldType) {
                    /** @var ReferencingPersistenceFieldType $persistenceFieldType */
                    return $this->checkFieldOverride($value, $key) ?:
                        new MultiSelectField($value, $persistenceFieldType->getValueSupplier());
                };
            default:
                return null;
        }
    }

    /**
     * @param $value
     * @param $key
     * @return callable|null
     */
    private function checkFieldOverride($value, $key)
    {
        if (isset($this->entityFieldOverrides[$key]) &&
            isset($this->entityFieldOverrides[$key][Field::class])) {
            return $this->entityFieldOverrides[$key][Field::class]($value);
        } else {
            return null;
        }
    }
}