<?php


namespace BasicTablePackage\View\TableView;


use BasicTablePackage\FieldConfigurationOverride\EntityFieldOverrides;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypeReader;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypes;
use function BasicTablePackage\Lib\collect as collect;

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
        $fieldTypes =
            collect($this->persistenceFieldTypeReader->getPersistenceFieldTypes())
                ->map(function ($persistenceFieldType) {
                    return self::createFieldTypeOf($persistenceFieldType);
                });
        return new TableViewFieldConfiguration($fieldTypes->toArray());
    }

    private function createFieldTypeOf(string $persistenceFieldType)
    {
        switch ($persistenceFieldType) {
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