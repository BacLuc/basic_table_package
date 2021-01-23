<?php

namespace BaclucC5Crud\View\FormView\ValueTransformers;

use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldType;

interface ValueTransformerConfiguration {
    public function getTransformerFor(PersistenceFieldType $persistenceFieldType): ValueTransformer;
}
