<?php


namespace BasicTablePackage\View\FormView\ValueTransformers;


use DateTime;

class DateValueTransformer implements ValueTransformer
{

    /**
     * @inheritDoc
     */
    public function transform($persistenceValue)
    {
        /** @var DateTime $persistenceValue */
        return $persistenceValue ? $persistenceValue->format("Y-m-d") : "";
    }
}