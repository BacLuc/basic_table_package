<?php


namespace BasicTablePackage\View\TableView;


use BasicTablePackage\Entity\ReferencedEntity;
use BasicTablePackage\Entity\RepositoryFactory;
use BasicTablePackage\Entity\RepositoryValueSupplier;
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
            case PersistenceFieldTypes::MANY_TO_ONE:
                return function ($value, $key) {
                    $repository = $this->repositoryFactory->createRepositoryFor(ReferencedEntity::class);
                    $valueSupplier = new RepositoryValueSupplier($repository);
                    return $this->checkFieldOverride($value, $key) ?: new DropdownField($value, $valueSupplier);
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