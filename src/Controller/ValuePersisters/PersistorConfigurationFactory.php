<?php


namespace BasicTablePackage\Controller\ValuePersisters;


use BasicTablePackage\Entity\ReferencedEntity;
use BasicTablePackage\Entity\RepositoryFactory;
use BasicTablePackage\Entity\RepositoryValueSupplier;
use BasicTablePackage\FieldConfigurationOverride\EntityFieldOverrides;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypeReader;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypes;
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
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * @param PersistenceFieldTypeReader $persistenceFieldTypeReader
     * @param EntityFieldOverrides $entityFieldOverrides
     * @param RepositoryFactory $repositoryFactory
     */
    public function __construct(
        PersistenceFieldTypeReader $persistenceFieldTypeReader,
        EntityFieldOverrides $entityFieldOverrides,
        RepositoryFactory $repositoryFactory
    ) {
        $this->persistenceFieldTypeReader = $persistenceFieldTypeReader;
        $this->entityFieldOverrides = $entityFieldOverrides;
        $this->repositoryFactory = $repositoryFactory;
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
            case PersistenceFieldTypes::MANY_TO_ONE:
                $repository = $this->repositoryFactory->createRepositoryFor(ReferencedEntity::class);
                $valueSupplier = new RepositoryValueSupplier($repository);
                return new ManyToOneFieldPersistor($key, $valueSupplier);
            default:
                return null;
        }
    }
}