<?php


namespace BasicTablePackage\View\FormView\ValueTransformers;


use BasicTablePackage\FieldTypeDetermination\PersistenceFieldType;

class ValueTransformerConfiguration
{
    public function getTransformerFor(PersistenceFieldType $persistenceFieldType): ValueTransformer
    {
        return new IdentityValueTransformer();
    }
}