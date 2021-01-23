<?php

namespace BaclucC5Crud\FieldTypeDetermination;

use BaclucC5Crud\Entity\ValueSupplier;

class ReferencingPersistenceFieldType implements PersistenceFieldType {
    /**
     * @var string
     */
    private $type;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    public function __construct(string $type, ValueSupplier $valueSupplier) {
        $this->type = $type;
        $this->valueSupplier = $valueSupplier;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getValueSupplier(): ValueSupplier {
        return $this->valueSupplier;
    }

    public function isNullable() {
        return true;
    }
}
