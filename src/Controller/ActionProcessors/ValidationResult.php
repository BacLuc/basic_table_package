<?php


namespace BasicTablePackage\Controller\ActionProcessors;


use function BasicTablePackage\Lib\collect as collect;

class ValidationResult
{
    /**
     * @var array
     */
    private $validationItems;

    /**
     * ValidationResult constructor.
     */
    public function __construct (array $validationItems)
    {
        $this->validationItems = $validationItems;
    }

    public function isError ()
    {
        return collect($this->validationItems)
                   ->filter(function (ValidationResultItem $validationResultItem) { return $validationResultItem->isError(); })
                   ->count() > 0;
    }
}

