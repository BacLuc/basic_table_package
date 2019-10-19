<?php


namespace BasicTablePackage\View\TableView;


use BasicTablePackage\Entity\PersistenceFieldTypeReader;
use BasicTablePackage\Entity\PersistenceFieldTypes;
use function BasicTablePackage\Lib\collect as collect;

class TableViewConfigurationFactory
{
    /**
     * @var PersistenceFieldTypeReader
     */
    private $persistenceFieldTypeReader;

    /**
     * TableViewConfigurationFactory constructor.
     * @param PersistenceFieldTypeReader $persistenceFieldTypeReader
     */
    public function __construct (PersistenceFieldTypeReader $persistenceFieldTypeReader)
    {
        $this->persistenceFieldTypeReader = $persistenceFieldTypeReader;
    }

    public function createConfiguration (): TableViewFieldConfiguration
    {
        $fieldTypes =
            collect($this->persistenceFieldTypeReader->getPersistenceFieldTypes())
                ->map(function ($persistenceFieldType) { return self::createFieldTypeOf($persistenceFieldType); });
        return new TableViewFieldConfiguration($fieldTypes->toArray());
    }

    private function createFieldTypeOf (string $persistenceFieldType)
    {
        switch ($persistenceFieldType) {
            case PersistenceFieldTypes::STRING:
            case PersistenceFieldTypes::INTEGER:
                return function ($value) {
                    return new TextField($value);
                };
            case PersistenceFieldTypes::DATE:
                return function ($value) {
                    return new DateField($value);
                };
            case PersistenceFieldTypes::DATETIME:
                return function ($value) {
                    return new DateTimeField($value);
                };
            default:
                return null;
        }
    }
}