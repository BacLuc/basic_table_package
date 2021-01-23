<?php

namespace BaclucC5Crud\View\FormView\ValueTransformers;

use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldType;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypes;
use function BaclucC5Crud\Lib\collect as collect;

class PostValueTransformerConfiguration implements ValueTransformerConfiguration {
    public function getTransformerFor(PersistenceFieldType $persistenceFieldType): ValueTransformer {
        switch ($persistenceFieldType->getType()) {
            case PersistenceFieldTypes::MANY_TO_ONE:
                return new class() implements ValueTransformer {
                    /**
                     * {@inheritDoc}
                     */
                    public function transform($persistenceValue) {
                        return false !== filter_var($persistenceValue, FILTER_VALIDATE_INT) ?
                            intval($persistenceValue) :
                            $persistenceValue;
                    }
                };

            case PersistenceFieldTypes::MANY_TO_MANY:
                return new class() implements ValueTransformer {
                    /**
                     * {@inheritDoc}
                     */
                    public function transform($persistenceValue) {
                        $array = is_array($persistenceValue) ? $persistenceValue : [];

                        return collect($array)->keyBy(function ($value) {
                            return false !== filter_var($value, FILTER_VALIDATE_INT) ?
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
