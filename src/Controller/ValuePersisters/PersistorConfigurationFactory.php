<?php


namespace BasicTablePackage\Controller\ValuePersisters;


use BasicTablePackage\Entity\PersistenceFieldTypeReader;
use BasicTablePackage\Entity\PersistenceFieldTypes;
use function BasicTablePackage\Lib\collect as collect;

class PersistorConfigurationFactory
{
    /**
     * @var PersistenceFieldTypeReader
     */
    private $persistenceFieldTypeReader;

    /**
     * @param PersistenceFieldTypeReader $persistenceFieldTypeReader
     */
    public function __construct(PersistenceFieldTypeReader $persistenceFieldTypeReader)
    {
        $this->persistenceFieldTypeReader = $persistenceFieldTypeReader;
    }

    public function createConfiguration(): PersistorConfiguration
    {
        $fieldTypes =
            collect($this->persistenceFieldTypeReader->getPersistenceFieldTypes())
                ->map(function ($persistenceFieldType, $key) {
                    return self::createFieldTypeOf($persistenceFieldType,
                        $key);
                });
        return new PersistorConfiguration($fieldTypes->toArray());
    }

    private function createFieldTypeOf(string $persistenceFieldType, string $key)
    {
        switch ($persistenceFieldType) {
            case PersistenceFieldTypes::STRING:
            case PersistenceFieldTypes::TEXT:
            case PersistenceFieldTypes::INTEGER:
                return new TextFieldPersistor($key);
            case PersistenceFieldTypes::DATE:
                return new DatePersistor($key);
            case PersistenceFieldTypes::DATETIME:
                return new DateTimePersistor($key);
            default:
                return null;
        }
    }
}