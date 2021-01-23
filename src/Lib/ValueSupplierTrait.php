<?php

namespace BaclucC5Crud\Lib;

trait ValueSupplierTrait {
    /**
     * @var DuplicateRejectingMap
     */
    private $values;

    public function initialize(array $values) {
        if (isset($values['']) || isset($values[null])) {
            throw new \InvalidArgumentException('Empty string and null are not valid keys');
        }

        $this->values = new DuplicateRejectingMap($values);
    }

    public function getValues(): array {
        return $this->values->toArray();
    }
}
