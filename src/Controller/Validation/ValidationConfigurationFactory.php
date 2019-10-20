<?php


namespace BasicTablePackage\Controller\Validation;


use BasicTablePackage\Entity\PersistenceFieldTypeReader;
use BasicTablePackage\Entity\PersistenceFieldTypes;
use function BasicTablePackage\Lib\collect as collect;

class ValidationConfigurationFactory
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

    public function createConfiguration (): ValidationConfiguration
    {
        $fieldTypes =
            collect($this->persistenceFieldTypeReader->getPersistenceFieldTypes())
                ->map(function ($persistenceFieldType, $key) {
                    return self::createFieldTypeOf($persistenceFieldType,
                                                   $key);
                });
        return new ValidationConfiguration($fieldTypes->toArray());
    }

    private function createFieldTypeOf (string $persistenceFieldType, string $key)
    {
        switch ($persistenceFieldType) {
            case PersistenceFieldTypes::STRING:
                return new TextFieldValidator($key);
            case PersistenceFieldTypes::INTEGER:
                return new IntegerFieldValidator($key);
            case PersistenceFieldTypes::DATE:
            case PersistenceFieldTypes::DATETIME:
                return new DateValidator($key);
            default:
                return null;
        }
    }
}