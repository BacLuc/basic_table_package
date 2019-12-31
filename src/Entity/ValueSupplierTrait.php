<?php


namespace BasicTablePackage\Entity;

use BasicTablePackage\Lib\DuplicateRejectingMap;

trait ValueSupplierTrait
{
    /**
     * @var DuplicateRejectingMap
     */
    private $values;

    public function initialize(array $values)
    {
        $this->values = new DuplicateRejectingMap($values);
    }

    public function getValues(): array
    {
        return $this->values->toArray();
    }
}