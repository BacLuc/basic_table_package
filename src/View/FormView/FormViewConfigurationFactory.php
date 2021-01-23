<?php

namespace BaclucC5Crud\View\FormView;

use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrides;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldType;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypeReader;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypes;
use BaclucC5Crud\FieldTypeDetermination\ReferencingPersistenceFieldType;
use function BaclucC5Crud\Lib\collect as collect;
use BaclucC5Crud\View\FormView\ValueTransformers\ValueTransformerConfiguration;

class FormViewConfigurationFactory {
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

    public function createConfiguration(): FormViewFieldConfiguration {
        $fieldTypes =
            collect($this->persistenceFieldTypeReader->getPersistenceFieldTypes())
                ->map(function ($persistenceFieldType, $key) {
                    return self::createFieldTypeOf(
                        $persistenceFieldType,
                        $key
                    );
                })
            ;

        return new FormViewFieldConfiguration($fieldTypes->toArray());
    }

    public static function extractSqlValueOfEntity($entity, string $key) {
        return property_exists($entity, $key) ? $entity->{$key} : null;
    }

    private function createFieldTypeOf(PersistenceFieldType $persistenceFieldType, string $key) {
        if (isset($this->entityFieldOverrides[$key], $this->entityFieldOverrides[$key][Field::class])
            ) {
            return $this->entityFieldOverrides[$key][Field::class](null);
        }
        $valueTransformer = $this->valueTransformerConfiguration->getTransformerFor($persistenceFieldType);
        $getValue = function ($entity, $key) use ($valueTransformer) {
            return $valueTransformer->transform(self::extractSqlValueOfEntity($entity, $key));
        };

        switch ($persistenceFieldType->getType()) {
            case PersistenceFieldTypes::STRING:
                return function ($entity) use ($key, $getValue) {
                    return new TextField($key, $key, $getValue($entity, $key));
                };

            case PersistenceFieldTypes::INTEGER:
                return function ($entity) use ($key, $getValue) {
                    return new IntegerField($key, $key, $getValue($entity, $key));
                };

            case PersistenceFieldTypes::DATE:
                return function ($entity) use ($key, $getValue) {
                    return new DateField($key, $key, $getValue($entity, $key));
                };

            case PersistenceFieldTypes::DATETIME:
                return function ($entity) use ($key, $getValue) {
                    return new DateTimeField($key, $key, $getValue($entity, $key));
                };

            case PersistenceFieldTypes::TEXT:
                return function ($entity) use ($key, $getValue) {
                    return new WysiwygField(
                        $this->wysiwygEditorFactory->createEditor(),
                        $key,
                        $key,
                        $getValue($entity, $key)
                    );
                };

            case PersistenceFieldTypes::MANY_TO_ONE:
                return function ($entity) use ($key, $persistenceFieldType, $getValue) {
                    // @var ReferencingPersistenceFieldType $persistenceFieldType
                    return new DropdownField(
                        $key,
                        $key,
                        $getValue($entity, $key),
                        $persistenceFieldType->getValueSupplier()
                    );
                };

            case PersistenceFieldTypes::MANY_TO_MANY:
                return function ($entity) use ($key, $persistenceFieldType, $getValue) {
                    // @var ReferencingPersistenceFieldType $persistenceFieldType
                    return new MultiSelectField(
                        $key,
                        $key,
                        $getValue($entity, $key),
                        $persistenceFieldType->getValueSupplier()
                    );
                };

            default:
                return null;
        }
    }
}
