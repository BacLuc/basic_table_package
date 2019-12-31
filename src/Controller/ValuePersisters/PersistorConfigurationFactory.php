<?php


namespace BasicTablePackage\Controller\ValuePersisters;


use BasicTablePackage\Entity\EntityFieldOverrides;
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
        if (isset($this->entityFieldOverrides[$key]) &&
            isset($this->entityFieldOverrides[$key][FieldPersistor::class])) {
            return $this->entityFieldOverrides[$key][FieldPersistor::class]($key);
        }
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