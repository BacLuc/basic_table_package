<?php

namespace BaclucC5Crud\View\FormView\ValueTransformers;

use BaclucC5Crud\Entity\Identifiable;

class DropdownValueTransformer implements ValueTransformer {
    /**
     * {@inheritDoc}
     */
    public function transform($persistenceValue) {
        if (is_object($persistenceValue)) {
            // @var $persistenceValue Identifiable
            return $persistenceValue->getId();
        }

        return $persistenceValue;
    }
}
