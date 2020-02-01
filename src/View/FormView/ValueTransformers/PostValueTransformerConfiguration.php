<?php


namespace BasicTablePackage\View\FormView\ValueTransformers;


use BasicTablePackage\FieldTypeDetermination\PersistenceFieldType;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypes;
use function BasicTablePackage\Lib\collect as collect;

class PostValueTransformerConfiguration implements ValueTransformerConfiguration
{
    public function getTransformerFor(PersistenceFieldType $persistenceFieldType): ValueTransformer
    {
        switch ($persistenceFieldType->getType()) {
            case PersistenceFieldTypes::MANY_TO_ONE:
                return new class implements ValueTransformer {
                    /**
                     * @inheritDoc
                     */
                    public function transform($persistenceValue)
                    {
                        return filter_var($persistenceValue, FILTER_VALIDATE_INT) !== false ?
                            intval($persistenceValue) :
                            $persistenceValue;
                    }
                };
            case PersistenceFieldTypes::MANY_TO_MANY:
                return new class implements ValueTransformer {

                    /**
                     * @inheritDoc
                     */
                    public function transform($persistenceValue)
                    {
                        $array = is_array($persistenceValue) ? $persistenceValue : [];
                        return collect($array)->keyBy(function ($value) {
                            return filter_var($value, FILTER_VALIDATE_INT) !== false ?
                                intval($value) :
                                $value;
                        })->toArray();
                    }
                };
            default:
                return new IdentityValueTransformer();
        }
    }
}