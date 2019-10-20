<?php


namespace BasicTablePackage\View\FormView;


use BasicTablePackage\Entity\PersistenceFieldTypeReader;
use BasicTablePackage\Entity\PersistenceFieldTypes;
use function BasicTablePackage\Lib\collect as collect;

class FormViewConfigurationFactory
{

    /**
     * @var PersistenceFieldTypeReader
     */
    private $persistenceFieldTypeReader;

    /**
     * @param PersistenceFieldTypeReader $persistenceFieldTypeReader
     */
    public function __construct (PersistenceFieldTypeReader $persistenceFieldTypeReader)
    {
        $this->persistenceFieldTypeReader = $persistenceFieldTypeReader;
    }

    public function createConfiguration (): FormViewFieldConfiguration
    {
        $fieldTypes =
            collect($this->persistenceFieldTypeReader->getPersistenceFieldTypes())
                ->map(function ($persistenceFieldType, $key) {
                    return self::createFieldTypeOf($persistenceFieldType,
                                                   $key);
                });
        return new FormViewFieldConfiguration($fieldTypes->toArray());
    }

    private function createFieldTypeOf (string $persistenceFieldType, string $key)
    {
        switch ($persistenceFieldType) {
            case PersistenceFieldTypes::STRING:
                return function ($entity) use ($key) {
                    return new TextField($key, $key, self::extractSqlValueOfEntity($entity, $key));
                };
            case PersistenceFieldTypes::INTEGER:
                return function ($entity) use ($key) {
                    return new IntegerField($key, $key, self::extractSqlValueOfEntity($entity, $key));
                };
            case PersistenceFieldTypes::DATE:
                return function ($entity) use ($key) {
                    return new DateField($key, $key, self::extractSqlValueOfEntity($entity, $key));
                };
            case PersistenceFieldTypes::DATETIME:
                return function ($entity) use ($key) {
                    return new DateTimeField($key, $key, self::extractSqlValueOfEntity($entity, $key));
                };
            default:
                return null;
        }
    }

    private static function extractSqlValueOfEntity ($entity, string $key)
    {
        return property_exists($entity, $key) ? $entity->{$key} : null;
    }
}