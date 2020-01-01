<?php


namespace BasicTablePackage\FieldTypeDetermination;


use Doctrine\ORM\Mapping\Annotation;

interface PersistenceFieldTypeHandler
{
    public function canHandle(Annotation $annotation): bool;

    public function getFieldTypeOf(Annotation $annotation);
}