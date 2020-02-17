<?php


namespace BaclucC5Crud\View\FormView\ValueTransformers;


use DateTime;

class DateTimeValueTransformer implements ValueTransformer
{
    const DATETIME_FORMAT = "Y-m-d H:i";

    /**
     * @inheritDoc
     */
    public function transform($persistenceValue)
    {
        /** @var DateTime $persistenceValue */
        return $persistenceValue ? $persistenceValue->format(self::DATETIME_FORMAT) : "";
    }
}