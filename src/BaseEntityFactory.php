<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 23.03.17
 * Time: 14:38
 */

namespace Concrete\Package\BasicTablePackage\Src;


use Concrete\Package\BasicTablePackage\Src\FieldTypes\AssociationFieldInterface;
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
    public function createFromLabelArray(array $array){
        //create a label->fieldtype map
        $fieldTypes = $this->targetClass->getFieldTypes();
        $labelMap = array();
        $classname = get_class($this->targetClass);
        /**
         * @var BaseEntity $targetModel
         */
        $targetModel = new $classname;
        foreach($fieldTypes as $key => $fieldType){
            $labelMap[$fieldType->getLabel()]=$fieldType;
        }
        $errormsgs = array();
        foreach ($labelMap as $label => $fieldType){
            /**
             * @var Field $fieldType
             */
            if(isset($array[$label])){
                if($fieldType->validatePost($array[$label])) {
                    $targetModel->set($fieldType->getSQLFieldname(), $fieldType->getSQLValue());
                }else{
                    $errormsgs[]=$fieldType->getErrorMsg();
                }
            }
        }
        if(count($errormsgs)>0){
            throw new \InvalidArgumentException(implode("<br>", $errormsgs));
        }

        return $targetModel;

    }

    public function createFromSQLFieldNameSQLValue(array $array, $associationsAreStrings = false){
        //create a label->fieldtype map
        $fieldTypes = $this->targetClass->getFieldTypes();
        $classname = get_class($this->targetClass);
        /**
         * @var BaseEntity $targetModel
         */
        $targetModel = new $classname;

        $errormsgs = array();
        foreach ($fieldTypes as $fieldname => $fieldType){
            /**
             * @var Field $fieldType
             */
            if(isset($array[$fieldname])){
                if($fieldType instanceof AssociationFieldInterface && $associationsAreStrings) {
                    if($fieldType->validatePost($array[$fieldname])){
                        $targetModel->set($fieldType->getSQLFieldname(), $fieldType->getSQLValue());
                    }else{
                        $errormsgs[]=$fieldType->getErrorMsg();
                    }

                }else{
                    $targetModel->set($fieldType->getSQLFieldname(), $array[$fieldname]);

                }
            }
        }
        if(count($errormsgs)>0){
            throw new \InvalidArgumentException(implode("<br>", $errormsgs));
        }

        return $targetModel;

    }

}