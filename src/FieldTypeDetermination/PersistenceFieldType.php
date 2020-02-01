<?php


namespace BasicTablePackage\FieldTypeDetermination;


interface PersistenceFieldType
{
    public function getType(): string;

    /**
     * @return bool
     */
    public function isNullable();
}