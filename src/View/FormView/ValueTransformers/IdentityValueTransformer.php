<?php


namespace BasicTablePackage\View\FormView\ValueTransformers;


class IdentityValueTransformer implements ValueTransformer
{

    /**
     * @inheritDoc
     */
    public function transform($persistenceValue)
    {
        return $persistenceValue;
    }
}