<?php


namespace BaclucC5Crud\Controller\ValuePersisters;


use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrides;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldType;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypeReader;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypes;
use BaclucC5Crud\FieldTypeDetermination\ReferencingPersistenceFieldType;
use function BaclucC5Crud\Lib\collect as collect;

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

    private function createFieldTypeOf(PersistenceFieldType $persistenceFieldType, string $key)
    {
        if (isset($this->entityFieldOverrides[$key]) &&
            isset($this->entityFieldOverrides[$key][FieldPersistor::class])) {
            return $this->entityFieldOverrides[$key][FieldPersistor::class]($key);
        }
        switch ($persistenceFieldType->getType()) {
            case PersistenceFieldTypes::STRING:
            case PersistenceFieldTypes::INTEGER:
                return new TextFieldPersistor($key);
            case PersistenceFieldTypes::TEXT:
                return new WysiwygPersistor($key);
            case PersistenceFieldTypes::DATE:
                return new DatePersistor($key);
            case PersistenceFieldTypes::DATETIME:
                return new DateTimePersistor($key);
            case PersistenceFieldTypes::MANY_TO_ONE:
                /** @var ReferencingPersistenceFieldType $persistenceFieldType */
                return new ManyToOneFieldPersistor($key, $persistenceFieldType->getValueSupplier());
            case PersistenceFieldTypes::MANY_TO_MANY:
                /** @var ReferencingPersistenceFieldType $persistenceFieldType */
                return new ManyToManyFieldPersistor($key, $persistenceFieldType->getValueSupplier());
            default:
                return null;
        }
    }
}