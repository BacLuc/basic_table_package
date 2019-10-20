<?php


namespace BasicTablePackage\Controller\Validation;


use BasicTablePackage\Lib\IteratorTrait;
use Iterator;
use function BasicTablePackage\Lib\collect as collect;

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

