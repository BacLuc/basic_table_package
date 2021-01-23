<?php

namespace BaclucC5Crud\FieldTypeDetermination;

interface PersistenceFieldType {
    public function getType(): string;

    /**
     * @return bool
     */
    public function isNullable();
}
