<?php


namespace BaclucC5Crud\Controller\Validation;


use BaclucC5Crud\Lib\IteratorTrait;
use Iterator;
use function BaclucC5Crud\Lib\collect as collect;

class ValidationResult implements Iterator
{
    use IteratorTrait;

    /**
     * ValidationResult constructor.
     */
    public function __construct(array $validationItems)
    {
        $this->initialize($validationItems);
    }

    public function isError()
    {
        return collect($this)
                   ->filter(function (ValidationResultItem $validationResultItem) {
                       return $validationResultItem->isError();
                   })
                   ->count() > 0;
    }
}

