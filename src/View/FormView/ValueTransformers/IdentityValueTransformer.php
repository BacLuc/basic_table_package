<?php

namespace BaclucC5Crud\View\FormView\ValueTransformers;

class IdentityValueTransformer implements ValueTransformer {
    /**
     * {@inheritDoc}
     */
    public function transform($persistenceValue) {
        return $persistenceValue;
    }
}
