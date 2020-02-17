<?php


namespace BaclucC5Crud\View\FormView;


use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrides;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypeReader;
use BaclucC5Crud\View\FormView\ValueTransformers\IdentityValueTransformer;
use BaclucC5Crud\View\FormView\ValueTransformers\PostValueTransformerConfiguration;
use function BaclucC5Crud\Lib\collect as collect;

class PostFormViewConfigurationFactory
{

    /**
     * @var EntityFieldOverrides
     */
    private $entityFieldOverrides;
    /**
     * @var FormViewConfigurationFactory
     */
    private $formViewConfigurationFactory;

    /**
     * @param PersistenceFieldTypeReader $persistenceFieldTypeReader
     * @param WysiwygEditorFactory $wysiwygEditorFactory
     * @param EntityFieldOverrides $entityFieldOverrides
     * @param PostValueTransformerConfiguration $valueTransformerConfiguration
     */
    public function __construct(
        PersistenceFieldTypeReader $persistenceFieldTypeReader,
        WysiwygEditorFactory $wysiwygEditorFactory,
        EntityFieldOverrides $entityFieldOverrides,
        PostValueTransformerConfiguration $valueTransformerConfiguration
    ) {
        $this->entityFieldOverrides = $entityFieldOverrides;
        $this->formViewConfigurationFactory = new FormViewConfigurationFactory($persistenceFieldTypeReader,
            $wysiwygEditorFactory,
            $this->entityFieldOverrides,
            $valueTransformerConfiguration);
    }

    public function createConfiguration(): FormViewFieldConfiguration
    {
        $formViewFieldConfiguration = $this->formViewConfigurationFactory->createConfiguration();
        $fieldTypes =
            collect($formViewFieldConfiguration)
                ->map(function ($existingFactory, $key) {
                    if (isset($this->entityFieldOverrides[$key]) &&
                        isset($this->entityFieldOverrides[$key][Field::class])) {
                        return $this->entityFieldOverrides[$key][Field::class](new IdentityValueTransformer());
                    } else {
                        return $existingFactory;
                    }
                });
        return new FormViewFieldConfiguration($fieldTypes->toArray());
    }

}