<?php

namespace BaclucC5Crud\View\FormView\ValueTransformers;

use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldType;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypes;

class PersistenceValueTransformerConfiguration implements ValueTransformerConfiguration {
    public function getTransformerFor(PersistenceFieldType $persistenceFieldType): ValueTransformer {
        switch ($persistenceFieldType->getType()) {
            case PersistenceFieldTypes::DATE:
                return new DateValueTransformer();

            case PersistenceFieldTypes::DATETIME:
                return new DateTimeValueTransformer();

            case PersistenceFieldTypes::MANY_TO_ONE:
                return new DropdownValueTransformer();

            case PersistenceFieldTypes::MANY_TO_MANY:
                return new MultiSelectValueTransformer();

            default:
                return new IdentityValueTransformer();
        }
    }
}
