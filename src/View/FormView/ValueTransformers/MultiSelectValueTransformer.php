<?php


namespace BaclucC5Crud\View\FormView\ValueTransformers;


use Doctrine\Common\Collections\ArrayCollection;

class MultiSelectValueTransformer implements ValueTransformer
{

    /**
     * @inheritDoc
     */
    public function transform($persistenceValue)
    {
        $arrayCollection = $persistenceValue ?: new ArrayCollection();
        return $sqlValue = collect($arrayCollection->toArray())->keyBy(function ($value) {
            return $value->id;
        })->toArray();
    }
}