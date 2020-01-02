<?php


namespace BasicTablePackage\FieldTypeDetermination;


interface PersistenceFieldType
{
    public function getType(): string;
}