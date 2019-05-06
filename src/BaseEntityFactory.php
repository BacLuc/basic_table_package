<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 23.03.17
 * Time: 14:38
 */

namespace Concrete\Package\BasicTablePackage\Src;


use Concrete\Package\BasicTablePackage\Src\FieldTypes\AssociationFieldInterface;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DirectEditAssociatedEntityField;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DirectEditAssociatedEntityMultipleField;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DropdownLinkField;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DropdownMultilinkField;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\Field;

class BaseEntityFactory
{
    protected $targetClass;

    public function __construct(BaseEntity $targetClass)
    {
        $this->targetClass = $targetClass;
    }

    /**
     * @param array $array
     * @return BaseEntity
     */
    public function createFromLabelArray(array $array)
    {
        //create a label->fieldtype map
        $fieldTypes = $this->targetClass->getFieldTypes();
        $labelMap = array();
        $classname = get_class($this->targetClass);
        /**
         * @var BaseEntity $targetModel
         */
        $targetModel = new $classname();
        foreach ($fieldTypes as $key => $fieldType) {
            $labelMap[$fieldType->getLabel()] = $fieldType;
        }
        $errormsgs = array();
        foreach ($labelMap as $label => $fieldType) {
            /**
             * @var Field $fieldType
             */
            if (isset($array[$label])) {

                //because the value from sql comes from a uniquestring, switch back to the normal link fields
                $fieldType = static::convertDirectEditToLink($fieldType);
                if ($fieldType->validatePost($array[$label])) {
                    $targetModel->set($fieldType->getSQLFieldname(), $fieldType->getSQLValue());
                } else {
                    $errormsgs[] = $fieldType->getErrorMsg();
                }
            } else {
                $targetModel->setFieldTypeIsNotSet($fieldType->getSQLFieldName(), true);
            }
        }
        if (count($errormsgs) > 0) {
            throw new \InvalidArgumentException(implode("<br>", $errormsgs));
        }

        return $targetModel;

    }

    /**
     * @param $fieldType
     * @return DropdownLinkField|DropdownMultilinkField
     */
    public static function convertDirectEditToLink($fieldType)
    {
        if ($fieldType instanceof DirectEditAssociatedEntityField) {
            $newFieldType =
                new DropdownLinkField($fieldType->getSQLFieldName(), $fieldType->getLabel(), $fieldType->getPostName());
            DropdownLinkField::copyLinkInfo($fieldType, $newFieldType);
            $fieldType = $newFieldType;
        } elseif ($fieldType instanceof DirectEditAssociatedEntityMultipleField) {
            $newFieldType = new DropdownMultilinkField($fieldType->getSQLFieldName(), $fieldType->getLabel(),
                $fieldType->getPostName());
            DropdownLinkField::copyLinkInfo($fieldType, $newFieldType);
            $fieldType = $newFieldType;
        }
        return $fieldType;
    }

    public function createFromSQLFieldNameSQLValue(array $array, $associationsAreStrings = false)
    {
        //create a label->fieldtype map
        $fieldTypes = $this->targetClass->getFieldTypes();
        $classname = get_class($this->targetClass);
        /**
         * @var BaseEntity $targetModel
         */
        $targetModel = new $classname();

        $errormsgs = array();
        foreach ($fieldTypes as $fieldname => $fieldType) {
            /**
             * @var Field $fieldType
             */
            if (isset($array[$fieldname])) {
                $fieldType = static::convertDirectEditToLink($fieldType);
                if ($fieldType instanceof AssociationFieldInterface && $associationsAreStrings) {
                    if ($fieldType->validatePost($array[$fieldname])) {
                        $targetModel->set($fieldType->getSQLFieldname(), $fieldType->getSQLValue());
                    } else {
                        $errormsgs[] = $fieldType->getErrorMsg();
                    }

                } else {
                    $targetModel->set($fieldType->getSQLFieldname(), $array[$fieldname]);

                }
            } else {
                $targetModel->setFieldTypeIsNotSet($fieldType->getSQLFieldName(), true);
            }
        }
        if (count($errormsgs) > 0) {
            throw new \InvalidArgumentException(implode("<br>", $errormsgs));
        }

        return $targetModel;

    }

}