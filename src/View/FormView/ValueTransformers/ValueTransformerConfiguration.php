<?php

namespace BasicTablePackage\View\FormView\ValueTransformers;

use BasicTablePackage\FieldTypeDetermination\PersistenceFieldType;

interface ValueTransformerConfiguration
{
    public function getTransformerFor(PersistenceFieldType $persistenceFieldType): ValueTransformer;
}