<?php

namespace BaclucC5Crud\Controller\ValuePersisters;

use BaclucC5Crud\Entity\ValueSupplier;

class ManyToOneFieldPersistor implements FieldPersistor {
    /**
     * @var string
     */
    private $name;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    /**
     * TextFieldPersistor constructor.
     */
    public function __construct(string $name, ValueSupplier $valueSupplier) {
        $this->name = $name;
        $this->valueSupplier = $valueSupplier;
    }

    public function persist($valueMap, $toEntity) {
        $values = $this->valueSupplier->getValues();
        $postvalue = $valueMap[$this->name];
        $toEntity->{$this->name} = null !== $postvalue ? $values[$postvalue] : null;
    }
}
