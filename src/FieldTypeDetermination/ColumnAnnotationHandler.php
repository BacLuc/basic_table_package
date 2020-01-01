<?php


namespace BasicTablePackage\FieldTypeDetermination;


use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\Column;

class ColumnAnnotationHandler implements PersistenceFieldTypeHandler
{

    public function canHandle(Annotation $annotation): bool
    {
        return $annotation instanceof Column;
    }

    public function getFieldTypeOf(Annotation $annotation)
    {
        if ($this->canHandle($annotation)) {
            /** @var Column $annotation */
            return $annotation->type;
        } else {
            return null;
        }
    }
}
