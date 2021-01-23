<?php

namespace BaclucC5Crud\View\FormView;

use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrides;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypeReader;
use function BaclucC5Crud\Lib\collect as collect;
use BaclucC5Crud\View\FormView\ValueTransformers\IdentityValueTransformer;
use BaclucC5Crud\View\FormView\ValueTransformers\PostValueTransformerConfiguration;

class PostFormViewConfigurationFactory {
    /**
     * @var EntityFieldOverrides
     */
    private $entityFieldOverrides;
    /**
     * @var FormViewConfigurationFactory
     */
    private $formViewConfigurationFactory;

    public function __construct(
        PersistenceFieldTypeReader $persistenceFieldTypeReader,
        WysiwygEditorFactory $wysiwygEditorFactory,
        EntityFieldOverrides $entityFieldOverrides,
        PostValueTransformerConfiguration $valueTransformerConfiguration
    ) {
        $this->entityFieldOverrides = $entityFieldOverrides;
        $this->formViewConfigurationFactory = new FormViewConfigurationFactory(
            $persistenceFieldTypeReader,
            $wysiwygEditorFactory,
            $this->entityFieldOverrides,
            $valueTransformerConfiguration
        );
    }

    public function createConfiguration(): FormViewFieldConfiguration {
        $formViewFieldConfiguration = $this->formViewConfigurationFactory->createConfiguration();
        $fieldTypes =
            collect($formViewFieldConfiguration)
                ->map(function ($existingFactory, $key) {
                    if (isset($this->entityFieldOverrides[$key], $this->entityFieldOverrides[$key][Field::class])
                        ) {
                        return $this->entityFieldOverrides[$key][Field::class](new IdentityValueTransformer());
                    }

                    return $existingFactory;
                })
            ;

        return new FormViewFieldConfiguration($fieldTypes->toArray());
    }
}
