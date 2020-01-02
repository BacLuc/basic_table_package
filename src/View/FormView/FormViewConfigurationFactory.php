<?php


namespace BasicTablePackage\View\FormView;


use BasicTablePackage\Entity\ReferencedEntity;
use BasicTablePackage\Entity\RepositoryFactory;
use BasicTablePackage\Entity\RepositoryValueSupplier;
use BasicTablePackage\FieldConfigurationOverride\EntityFieldOverrides;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypeReader;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypes;
use function BasicTablePackage\Lib\collect as collect;

class FormViewConfigurationFactory
{

    /**
     * @var PersistenceFieldTypeReader
     */
    private $persistenceFieldTypeReader;
    /**
     * @var WysiwygEditorFactory
     */
    private $wysiwygEditorFactory;
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
     * @param WysiwygEditorFactory $wysiwygEditorFactory
     * @param EntityFieldOverrides $entityFieldOverrides
     * @param RepositoryFactory $repositoryFactory
     */
    public function __construct(
        PersistenceFieldTypeReader $persistenceFieldTypeReader,
        WysiwygEditorFactory $wysiwygEditorFactory,
        EntityFieldOverrides $entityFieldOverrides,
        RepositoryFactory $repositoryFactory
    ) {
        $this->persistenceFieldTypeReader = $persistenceFieldTypeReader;
        $this->wysiwygEditorFactory = $wysiwygEditorFactory;
        $this->entityFieldOverrides = $entityFieldOverrides;
        $this->repositoryFactory = $repositoryFactory;
    }

    public function createConfiguration(): FormViewFieldConfiguration
    {
        $fieldTypes =
            collect($this->persistenceFieldTypeReader->getPersistenceFieldTypes())
                ->map(function ($persistenceFieldType, $key) {
                    return self::createFieldTypeOf($persistenceFieldType,
                        $key);
                });
        return new FormViewFieldConfiguration($fieldTypes->toArray());
    }

    private function createFieldTypeOf(string $persistenceFieldType, string $key)
    {
        if (isset($this->entityFieldOverrides[$key]) &&
            isset($this->entityFieldOverrides[$key][Field::class])) {
            return $this->entityFieldOverrides[$key][Field::class];
        }
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
            case PersistenceFieldTypes::TEXT:
                return function ($entity) use ($key) {
                    return new WysiwygField($this->wysiwygEditorFactory->createEditor(),
                        $key,
                        $key,
                        self::extractSqlValueOfEntity($entity, $key));
                };
            case PersistenceFieldTypes::MANY_TO_ONE:
                return function ($entity) use ($key) {
                    $repository = $this->repositoryFactory->createRepositoryFor(ReferencedEntity::class);
                    $valueSupplier = new RepositoryValueSupplier($repository);
                    return new Dropdownfield($key, $key, self::extractSqlValueOfEntity($entity, $key), $valueSupplier);
                };
            default:
                return null;
        }
    }

    public static function extractSqlValueOfEntity($entity, string $key)
    {
        return property_exists($entity, $key) ? $entity->{$key} : null;
    }
}