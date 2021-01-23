<?php

namespace BaclucC5Crud\View\FormView\ValueTransformers;

interface ValueTransformer {
    /**
     * Transforms a value from a persistence form into a value that can be displayed in a form
     * $persistenceValue can be a value as it is represented by the ORM or in a POST array.
     *
     * @param $persistenceValue
     *
     * @return mixed
     */
    public function transform($persistenceValue);
}
