<?php


namespace BaclucC5Crud\View\FormView\ValueTransformers;


class DropdownValueTransformer implements ValueTransformer
{

    /**
     * @inheritDoc
     */
    public function transform($persistenceValue)
    {
        return is_object($persistenceValue) ? $persistenceValue->id : $persistenceValue;
    }
}