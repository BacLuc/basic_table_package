<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.04.17
 * Time: 17:08
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes\Strategy;


use Concrete\Package\BasicTablePackage\Src\NewFieldTypes\FieldType;

abstract class AbstractFieldStrategy
{
    /**
     * @var FieldType
     */
    protected $fieldType;

    /**
     * AbstractProvider constructor.
     * @param FieldType $fieldType
     */
    public function __construct(FieldType $fieldType)
    {
        $this->setFieldType($fieldType);
    }

    /**
     * @return FieldType
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * @param FieldType $fieldType
     * @return $this
     */
    public function setFieldType(FieldType $fieldType)
    {
        $this->fieldType = $fieldType;
        return $this;
    }

}