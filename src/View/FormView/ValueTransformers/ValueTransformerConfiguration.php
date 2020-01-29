<?php


namespace BasicTablePackage\View\FormView\ValueTransformers;


use BasicTablePackage\FieldTypeDetermination\PersistenceFieldType;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypes;

class ValueTransformerConfiguration
{
    public function getTransformerFor(PersistenceFieldType $persistenceFieldType): ValueTransformer
    {
        switch ($persistenceFieldType->getType()) {
            case PersistenceFieldTypes::DATE:
                return new DateValueTransformer();
            default:
                return new IdentityValueTransformer();
        }
    }
}