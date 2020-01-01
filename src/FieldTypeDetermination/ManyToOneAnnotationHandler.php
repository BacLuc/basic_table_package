<?php


namespace BasicTablePackage\FieldTypeDetermination;


use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\ManyToOne;

class ManyToOneAnnotationHandler implements PersistenceFieldTypeHandler
{

    public function canHandle(Annotation $annotation): bool
    {
        return $annotation instanceof ManyToOne;
    }

    public function getFieldTypeOf(Annotation $annotation)
    {
        if ($annotation instanceof ManyToOne) {
            /** @var ManyToOne $annotation */
            return PersistenceFieldTypes::MANY_TO_ONE;
        } else {
            return null;
        }
    }
}