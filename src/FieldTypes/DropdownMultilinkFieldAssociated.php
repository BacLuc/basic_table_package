<?php
namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;

use Concrete\Core\Block\BlockController;
use Concrete\Flysystem\Exception;
use Concrete\Package\BasicTablePackage\Src\AssociationEntity;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\Field as Field;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DropdownField as DropdownField;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DropdownLinkField as DropdownLinkField;
use Concrete\Package\EntitiesExample\Src\Entity;
use Loader;
use Page;
use User;
use Core;
use File;
use Concrete\Controller\SinglePage\Dashboard\Block\Permissions as Permissions;
use Concrete\Core\Block\View\BlockView as View;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\SelfSaveInterface as SelfSaveInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class DropdownMultilinkField
 * @package Concrete\Package\BasicTablePackage\Src\FieldTypes
 * Field for an n;m relation with bootstrap tagsinput
 * TODO change to twitter tagsinput, bootstrap tagsinput is depricated
 */
class DropdownMultilinkFieldAssociated extends DropdownMultilinkField{
    protected $linktable;
    protected $ntomtable;
    protected $sqlfilter = " 1=1 ";
    protected $sqlvars = array();
    protected $showcolumn;
    protected $isNullable = false;
    protected $linkfieldself;
    protected $linkfieldext;
    protected $rowid;
    protected $idfieldext;
    protected $idfieldself;
    protected $values = array();
    protected $allowAdd = false;
    protected $associationEntity;
    protected $targetFieldAssociationEntity;


    public function setLinkInfo($sourceEntity, $sourceField, $targetEntity, $targetField = null, callable $getDisplayString=null, array $filter = null){
        $this->sourceEntity = $sourceEntity;
        $this->sourceField = $sourceField;

        $targetEntityObject = new $targetEntity();

        if(! ($targetEntityObject instanceof  AssociationEntity)){
            throw new Exception("Cannot use ".get_class($this)." with an association pointing to a class which is not a subclass of AssociationEntity");
        }

        //now get the entity the association points really to
        $className = $targetEntity;
        $em = $this->getEntityManager();

        $metadata = $this->getEntityManager()->getMetadataFactory()->getMetadataFor($className);

        $associations = $metadata->getAssociationMappings();
        if(count($associations)!=2){
            throw new Exception("Cannot use ".get_class($this)." can only handle Association classes with 2 associations");
        }
        foreach($associations as $num => $associationMeta){
            if($metadata->isSingleValuedAssociation($associationMeta['fieldName'])){
                if($associationMeta['targetEntity'] == (is_object($this->sourceEntity)?get_class($this->sourceEntity):$this->sourceEntity)){

                }else{
                    $this->targetEntity = $associationMeta['targetEntity'];
                    $this->targetFieldAssociationEntity = $associationMeta['fieldName'];
                    $this->associationEntity = $targetEntity;
                }
            }
        }

        $this->targetField = $targetField;
        $targetClassName =  $this->targetEntity;
        $this->getDisplayString =$targetClassName::getDefaultgetDisplayStringFunction();
        $this->filter = $filter;
    }

    public function validatePost($value){


        $postvalues = explode(",", $value);
        $targetModelForIdField = new $this->targetEntity;
        $options = $this->getOptions();

        $flipoptions = array_flip($options);

        $sqlArray = new ArrayCollection();
        foreach($postvalues as $num => $postvalue){
            if(in_array($postvalue, $options)){
                $findItem = $this->getEntityManager()
                    ->getRepository($this->targetEntity)
                    ->findOneBy(array(
                        $targetModelForIdField->getIdFieldname()=>$flipoptions[$postvalue]
                    ));
                $associationEntity = new $this->associationEntity;
                $associationEntity->set($this->targetField,$this->sourceEntity);
                $associationEntity->set($this->targetFieldAssociationEntity,$findItem);
                $sqlArray->add($associationEntity);
            }else{
               //TODO throw exception, if invalid values should produce an error message
            }
        }
        $this->setSQLValue($sqlArray);
        return true;
    }

    /**
     * get the Values of
     * @return ArrayCollection
     */
    private function getValues(){
        if(count($this->value)==0 && !is_null($this->rowid)) {
            $modelForIdField = new $this->targetEntity;
            /**
             * @var $model \Entity
             */



            $model = $this->getEntityManager()
                ->getRepository($this->targetEntity)
                ->findOneBy(array(
                    $modelForIdField->getIdFieldName() => $this->rowid
                ));
            $values = $model->get($this->sourceField);
            //now we got the associated fields, now we want the real object behind
            $realObjects = new ArrayCollection();
            if(count($values)>0) {
                foreach ($values->toArray() as $assnum => $associationObject) {
                    $realObjects->add($associationObject->get($this->targetFieldAssociationEntity));
                }
            }
            if(count($realObjects)>0 && $realObjects != null) {
                foreach ($realObjects->toArray() as $valnum => $value) {
                    $this->value[$value->getId()]=$this->getDisplayString($value);
                }
            }
        }

        return $this->value;

    }



    public function setSQLValue($value){
        if(count($value)==0){
            $this->value=new ArrayCollection();
        }elseif($value instanceof ArrayCollection){
            //check if values are of the right entitiy
            foreach($value->toArray() as $valnum => $valueitem){
                if(!$valueitem instanceof $this->associationEntity){
                    throw new \InvalidArgumentException("Item number $valnum is ".get_class($valueitem).", should be ".$this->targetEntity." sein");
                }

                //s$this->em->persist($valueitem);
            }
            $this->value = $value;
        }


    }



}