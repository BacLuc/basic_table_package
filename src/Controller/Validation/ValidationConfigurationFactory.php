<?php


namespace BasicTablePackage\Controller\Validation;


use BasicTablePackage\Entity\ReferencedEntity;
use BasicTablePackage\Entity\RepositoryFactory;
use BasicTablePackage\Entity\RepositoryValueSupplier;
use BasicTablePackage\FieldConfigurationOverride\EntityFieldOverrides;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypeReader;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypes;
use function BasicTablePackage\Lib\collect as collect;

class ValidationConfigurationFactory
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

    public function createConfiguration(): ValidationConfiguration
    {
        $fieldTypes =
            collect($this->persistenceFieldTypeReader->getPersistenceFieldTypes())
                ->map(function ($persistenceFieldType, $key) {
                    return self::createFieldTypeOf($persistenceFieldType,
                        $key);
                });
        return new ValidationConfiguration($fieldTypes->toArray());
    }

    private function createFieldTypeOf(string $persistenceFieldType, string $key)
    {
        if (isset($this->entityFieldOverrides[$key]) &&
            isset($this->entityFieldOverrides[$key][FieldValidator::class])) {
            return $this->entityFieldOverrides[$key][FieldValidator::class]($key);
        }
        switch ($persistenceFieldType) {
            case PersistenceFieldTypes::TEXT:
            case PersistenceFieldTypes::STRING:
                return new TextFieldValidator($key);
            case PersistenceFieldTypes::INTEGER:
                return new IntegerFieldValidator($key);
            case PersistenceFieldTypes::DATE:
            case PersistenceFieldTypes::DATETIME:
                return new DateValidator($key);
            case PersistenceFieldTypes::MANY_TO_ONE:
                $repository = $this->repositoryFactory->createRepositoryFor(ReferencedEntity::class);
                $valueSupplier = new RepositoryValueSupplier($repository);
                return new DropdownFieldValidator($key, $valueSupplier);
            default:
                return null;
        }
    }
}