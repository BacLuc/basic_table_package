<?php


namespace BaclucC5Crud\View\TableView;


use BaclucC5Crud\Entity\Identifiable;
use BaclucC5Crud\Entity\ValueSupplier;

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
            return function () use ($value, $valueSupplier) {
                return new DropdownField($value, $valueSupplier);
            };
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
            /** @var  $sqlValue Identifiable */
            $sqlValue = $sqlValue->getId();
        }
        return isset($values[$sqlValue]) ? $values[$sqlValue] : "";
    }


}