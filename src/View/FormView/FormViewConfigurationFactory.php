<?php


namespace BasicTablePackage\View\FormView;


use BasicTablePackage\FieldConfigurationOverride\EntityFieldOverrides;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldType;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypeReader;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypes;
use BasicTablePackage\FieldTypeDetermination\ReferencingPersistenceFieldType;
use BasicTablePackage\View\FormView\ValueTransformers\ValueTransformerConfiguration;
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
     * @var ValueTransformerConfiguration
     */
    private $valueTransformerConfiguration;

    /**
     * @param PersistenceFieldTypeReader $persistenceFieldTypeReader
     * @param WysiwygEditorFactory $wysiwygEditorFactory
     * @param EntityFieldOverrides $entityFieldOverrides
     * @param ValueTransformerConfiguration $valueTransformerConfiguration
     */
    public function __construct(
        PersistenceFieldTypeReader $persistenceFieldTypeReader,
        WysiwygEditorFactory $wysiwygEditorFactory,
        EntityFieldOverrides $entityFieldOverrides,
        ValueTransformerConfiguration $valueTransformerConfiguration
    ) {
        $this->persistenceFieldTypeReader = $persistenceFieldTypeReader;
        $this->wysiwygEditorFactory = $wysiwygEditorFactory;
        $this->entityFieldOverrides = $entityFieldOverrides;
        $this->valueTransformerConfiguration = $valueTransformerConfiguration;
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

    private function createFieldTypeOf(PersistenceFieldType $persistenceFieldType, string $key)
    {
        if (isset($this->entityFieldOverrides[$key]) &&
            isset($this->entityFieldOverrides[$key][Field::class])) {
            return $this->entityFieldOverrides[$key][Field::class];
        }
        $valueTransformer = $this->valueTransformerConfiguration->getTransformerFor($persistenceFieldType);
        switch ($persistenceFieldType->getType()) {
            case PersistenceFieldTypes::STRING:
                return function ($entity) use ($key, $valueTransformer) {
                    return new TextField($key,
                        $key,
                        $valueTransformer->transform(self::extractSqlValueOfEntity($entity, $key)));
                };
            case PersistenceFieldTypes::INTEGER:
                return function ($entity) use ($key, $valueTransformer) {
                    return new IntegerField($key,
                        $key,
                        $valueTransformer->transform(self::extractSqlValueOfEntity($entity, $key)));
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
                return function ($entity) use ($key, $persistenceFieldType) {
                    /** @var ReferencingPersistenceFieldType $persistenceFieldType */
                    return new DropdownField($key,
                        $key,
                        self::extractSqlValueOfEntity($entity, $key),
                        $persistenceFieldType->getValueSupplier());
                };
            case PersistenceFieldTypes::MANY_TO_MANY:
                return function ($entity) use ($key, $persistenceFieldType) {
                    /** @var ReferencingPersistenceFieldType $persistenceFieldType */
                    return new MultiSelectField($key,
                        $key,
                        self::extractSqlValueOfEntity($entity, $key),
                        $persistenceFieldType->getValueSupplier());
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