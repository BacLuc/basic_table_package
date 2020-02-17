<?php


namespace BaclucC5Crud\FieldTypeDetermination;


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
            return new SimplePersistenceFieldType($annotation->type, $annotation->nullable);
        } else {
            return null;
        }
    }
}
