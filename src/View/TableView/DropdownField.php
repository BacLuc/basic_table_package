<?php

namespace BaclucC5Crud\View\TableView;

use BaclucC5Crud\Entity\Identifiable;
use BaclucC5Crud\Entity\ValueSupplier;
use BaclucC5Crud\Entity\WithUniqueStringRepresentation;
use function BaclucC5Crud\Lib\collect as collect;
use RuntimeException;

class DropdownField implements Field {
    /**
     * @var string
     */
    private $sqlValue;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    /**
     * TextField constructor.
     *
     * @param $sqlValue
     */
    public function __construct($sqlValue, ValueSupplier $valueSupplier) {
        $this->sqlValue = $sqlValue;
        $this->valueSupplier = $valueSupplier;
    }

    public static function createDropdownField($valueSupplier): callable {
        return function ($value) use ($valueSupplier) {
            return function () use ($value, $valueSupplier) {
                return new DropdownField($value, $valueSupplier);
            };
        };
    }

    public function getTableView(): string {
        $values = collect($this->valueSupplier->getValues())
            ->map(function ($value) {
                if (is_object($value) && $value instanceof WithUniqueStringRepresentation) {
                    return $value->createUniqueString();
                }
                if (is_object($value)) {
                    throw new RuntimeException('$value is not instanceof WithUniqueStringRepresentation, thus it cannot be displayed, is instance of '.
                                               get_class($value));
                }

                return $value;
            })
        ;
        $sqlValue = $this->sqlValue;
        if (is_object($sqlValue)) {
            /** @var Identifiable $sqlValue */
            $sqlValue = $sqlValue->getId();
        }

        return isset($values[$sqlValue]) ? $values[$sqlValue] : '';
    }
}
