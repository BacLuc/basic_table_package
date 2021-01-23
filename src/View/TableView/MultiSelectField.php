<?php

namespace BaclucC5Crud\View\TableView;

use BaclucC5Crud\Entity\Identifiable;
use BaclucC5Crud\Entity\ValueSupplier;
use BaclucC5Crud\Entity\WithUniqueStringRepresentation;
use function BaclucC5Crud\Lib\collect as collect;
use Doctrine\Common\Collections\ArrayCollection;

class MultiSelectField implements Field {
    /**
     * @var ArrayCollection
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

    public function getTableView(): string {
        $values = $this->valueSupplier->getValues();

        return collect($this->sqlValue->toArray())
            ->map(function (Identifiable $value) use ($values) {
                if (isset($values[$value->getId()])) {
                    return $values[$value->getId()];
                }

                return null;
            })
            ->map(function (WithUniqueStringRepresentation $value) {
                return $value->createUniqueString();
            })
            ->join(',')
        ;
    }
}
