<?php

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;

use Concrete\Flysystem\Exception;
use Concrete\Package\BasicTablePackage\Src\AssociationBaseEntity;
use Concrete\Package\BasicTablePackage\Src\BaseEntityRepository;
use Concrete\Package\EntitiesExample\Src\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Proxy\Proxy;

/**
 * Class DropdownMultilinkField
 * @IgnoreAnnotation("package")\n*  Concrete\Package\BasicTablePackage\Src\FieldTypes
 * Field for an n;m relation with bootstrap tagsinput
 * TODO change to twitter tagsinput, bootstrap tagsinput is depricated
 */
class DropdownMultilinkFieldAssociated extends DropdownMultilinkField implements AssociationFieldInterface
{
    protected $linktable;
    protected $ntomtable;
    protected $sqlfilter = " 1=1 ";
    protected $sqlvars   = array();
    protected $showcolumn;
    protected $linkfieldself;
    protected $linkfieldext;
    protected $rowid;
    protected $idfieldext;
    protected $idfieldself;
    protected $values    = array();
    protected $allowAdd  = false;
    protected $associationEntity;
    protected $targetFieldAssociationEntity;
    protected $sourceEntityAssociationField;


    public function setLinkInfo(
        $sourceEntity,
        $sourceField,
        $targetEntity,
        $targetField = null,
        $associationType = null,
        callable $getDisplayString = null,
        callable $filter = null
    ) {
        $this->sourceEntity = $sourceEntity;
        $this->sourceField = $sourceField;

        $targetEntityObject = new $targetEntity();

        if (!($targetEntityObject instanceof AssociationBaseEntity)) {
            throw new Exception("Cannot use " . get_class($this)
                                . " with an association pointing to a class which is not a subclass of AssociationEntity");
        }

        //now get the entity the association points really to
        $className = $targetEntity;
        $em = $this->getEntityManager();

        $metadata = $this->getEntityManager()->getMetadataFactory()->getMetadataFor($className);

        $associations = $metadata->getAssociationMappings();
        if (count($associations) != 2) {
            throw new Exception("Cannot use " . get_class($this)
                                . " can only handle Association classes with 2 associations");
        }
        foreach ($associations as $num => $associationMeta) {
            if ($metadata->isSingleValuedAssociation($associationMeta['fieldName'])) {
                if ($associationMeta['targetEntity'] == (is_object($this->sourceEntity) ?
                        get_class($this->sourceEntity) : $this->sourceEntity)) {
                    $this->sourceEntityAssociationField = $associationMeta['fieldName'];
                } else {
                    $this->targetEntity = $associationMeta['targetEntity'];
                    $this->targetFieldAssociationEntity = $associationMeta['fieldName'];
                    $this->associationEntity = $targetEntity;
                }
            }
        }


        $this->targetField = $targetField;
        $targetClassName = $this->targetEntity;

        if ($associationType == null) {
            $this->associationType = static::DEFAULT_ASSOCIATION_TYPE;
        } else {
            $this->associationType = $associationType;
        }

        $this->getDisplayString = $targetClassName::getDefaultgetDisplayStringFunction();
        $this->filter = $filter;
    }

    public function validatePost($value)
    {


        $postvalues = explode(",", $value);
        $targetModelForIdField = new $this->targetEntity();
        $options = $this->getOptions();

        $flipoptions = array_flip($options);

        $sqlArray = new ArrayCollection();
        foreach ($postvalues as $num => $postvalue) {
            $postvalue = trim($postvalue);
            if (in_array($postvalue, $options)) {
                $findItem = BaseEntityRepository::getEntityById($this->targetEntity, $flipoptions[$postvalue]);

                $associationEntity = new $this->associationEntity();
                $associationEntity->set($this->sourceEntityAssociationField, $this->sourceEntity);
                $this->getEntityManager()->persist($this->sourceEntity);
                $associationEntity->set($this->targetFieldAssociationEntity, $findItem);
                $this->getEntityManager()->persist($findItem);
                $this->getEntityManager()->persist($associationEntity);
                $sqlArray->add($associationEntity);
            } else {
                //TODO throw exception, if invalid values should produce an error message
            }
        }
        //delete the existing
        $toDelete = $this->sourceEntity->get($this->getSQLFieldName());
        if (count($toDelete) > 0) {
            if ($toDelete instanceof Collection) {
                $toDelete = $toDelete->toArray();
            }
            foreach ($toDelete as $todelnum => $delItem) {
                $this->getEntityManager()->remove($delItem);
            }
        }

        $this->setSQLValue($sqlArray);
        return true;
    }

    public function setSQLValue($value)
    {
        if ($value instanceof PersistentCollection) {
            $value = new ArrayCollection($value->toArray());
        }

        if (count($value) == 0) {
            $this->value = new ArrayCollection();
        } elseif ($value instanceof ArrayCollection) {
            //check if values are of the right entitiy
            foreach ($value->toArray() as $valnum => $valueitem) {
                if (!$valueitem instanceof $this->associationEntity) {
                    throw new \InvalidArgumentException("Item number $valnum is " . get_class($valueitem)
                                                        . ", should be " . $this->targetEntity . " sein");
                }

                //s$this->em->persist($valueitem);
            }
            $this->value = $value;
        }


        return $this;
    }

    public function getFormView($form, $clientSideValidationActivated = true)
    {
        $html = "<label for='" . $this->getHtmlId() . "'>" . $this->getLabel() . "</label>";


        $html .= $this->getInputHtml($form, $clientSideValidationActivated);
        return $html;
    }

    /**
     * @param $form
     * @param bool $clientSideValidationActivated
     * @return string
     */
    public function getInputHtml($form, $clientSideValidationActivated = true)
    {
        $associations = $this->getValues();

        if (($associations == null || count($associations) == 0) && count($this->getDefault()) > 0) {
            $associations = $this->getDefault();
        }
        if ($associations instanceof ArrayCollection) {

            $associations = $associations->toArray();
        } elseif (is_array($associations)) {

        } else {

            $associations = array();
        }

        $valueStrings = array();

        //to display the associations, we have to convert them to strings with our getDisplayString function
        $displayFunction = $this->getDisplayString;
        foreach ($associations as $num => $association) {
            $realEntity = $association->get($this->targetFieldAssociationEntity);
            if ($realEntity instanceof Proxy) {
                $realEntity->__load();
            }
            $valueStrings[] = $displayFunction($realEntity);
        }


        $valuestring = implode(", ", $valueStrings);
        $html = "<input type='text' width = '100%' id='" . $this->getHtmlId() . "' name ='" . $this->getPostName()
                . "' value='$valuestring'/>";


        $options = $this->getOptions();
        $sourcetext = "'" . implode("', '", $options) . "'";
        $allowadd = 'false';
        if ($this->allowAdd) {
            $allowadd = 'true';
        }


        $html .= "
				<script type = 'text/javascript'>
					$(document).ready(function(e){
					    var values = [$sourcetext];
					    values = new Bloodhound({
                          datumTokenizer: Bloodhound.tokenizers.whitespace,
                          queryTokenizer: Bloodhound.tokenizers.whitespace,
                          local: values
                        });
                        values.initialize();

                        $('#" . $this->getHtmlId() . "').tagsinput({
                          freeInput: $allowadd,
                          typeaheadjs: [{

                            minLength:0,
                            highlight:true,
                            limit:10000,
                          },{
                           name: '" . $this->getPostName() . "',
                            source: function (q, sync) {
                                  if (q === '' ||q === '*' ) {
                                    sync(values.index.all());
                                  }

                                  else {
                                    values.search(q, sync);
                                }
                               },
                            limit:10000,
                          }]
                        });

					});

				</script>
				";


        $html .= $this->getHtmlErrorMsg();
        return $html;
    }

    /**
     * get the Values of
     * @return ArrayCollection
     */
    protected function getValues()
    {
        if (count($this->value) == 0 && !is_null($this->rowid)) {
            $modelForIdField = new $this->targetEntity();
            /**
             * @var $model \Entity
             */


            $model = $this->getEntityManager()
                          ->getRepository($this->targetEntity)
                          ->findOneBy(array(
                              $modelForIdField->getIdFieldName() => $this->rowid,
                          ))
            ;
            $values = $model->get($this->sourceField);
            //now we got the associated fields, now we want the real object behind
            $realObjects = new ArrayCollection();
            if (count($values) > 0) {
                foreach ($values->toArray() as $assnum => $associationObject) {
                    $realObjects->add($associationObject->get($this->targetFieldAssociationEntity));
                }
            }
            if (count($realObjects) > 0 && $realObjects != null) {
                foreach ($realObjects->toArray() as $valnum => $value) {
                    $this->value[$value->getId()] = $this->getDisplayString($value);
                }
            }
        }

        return $this->value;

    }

    public function getTableView()
    {
        $values = $this->getValues();
        if ($values instanceof PersistentCollection || $values instanceof ArrayCollection) {
            $values = $values->toArray();
        }

        $string = "";
        if (is_array($values)) {
            $first = true;

            foreach ($values as $valuenum => $value) {
                $appendString = "";


                if (is_object($value)) {
                    $realEntity = $value->get($this->targetFieldAssociationEntity);
                    if ($realEntity instanceof Proxy) {
                        $realEntity->__load();
                    }
                    $value = $realEntity;
                    $classname = $this->targetEntity;
                    $function = $classname::getDefaultGetDisplayStringFunction();
                    $appendString = $function($value);

                } else {
                    $appendString = $value;
                }

                if ($first) {
                    $first = false;
                } else {
                    $string .= ", ";
                }
                $string .= $appendString;
            }
        }
        return $string;
    }


}
