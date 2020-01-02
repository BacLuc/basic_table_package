<?php


namespace BasicTablePackage\View\TableView;


use BasicTablePackage\Entity\ValueSupplier;

class DropdownField implements Field
{
    /**
     * @var string
     */
    private $sqlValue;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    public static function createDropdownField($valueSupplier): callable
    {
        return function ($value) use ($valueSupplier) {
            return new DropdownField($value, $valueSupplier);
        };
    }

    /**
     * TextField constructor.
     * @param $sqlValue
     * @param ValueSupplier $valueSupplier
     */
    public function __construct($sqlValue, ValueSupplier $valueSupplier)
    {
        $this->sqlValue = $sqlValue;
        $this->valueSupplier = $valueSupplier;
    }

    public function getTableView(): string
    {
        $values = $this->valueSupplier->getValues();
        $sqlValue = $this->sqlValue;
        if (is_object($sqlValue)) {
            $sqlValue = $sqlValue->id;
        }
        return isset($values[$sqlValue]) ? t($values[$sqlValue]) : "";
    }


}