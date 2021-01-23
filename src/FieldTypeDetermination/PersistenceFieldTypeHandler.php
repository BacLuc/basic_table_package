<?php

namespace BaclucC5Crud\FieldTypeDetermination;

use Doctrine\ORM\Mapping\Annotation;

interface PersistenceFieldTypeHandler {
    public function canHandle(Annotation $annotation): bool;

    public function getFieldTypeOf(Annotation $annotation);
}
