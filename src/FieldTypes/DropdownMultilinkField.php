<?php

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;

use Concrete\Package\BasicTablePackage\Src\BaseEntity;
use Concrete\Package\BasicTablePackage\Src\BaseEntityRepository;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DropdownLinkField as DropdownLinkField;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\PersistentCollection;

/**
 * Class DropdownMultilinkField
 * @IgnoreAnnotation("package")\n*  Concrete\Package\BasicTablePackage\Src\FieldTypes
 * Field for an n;m relation with bootstrap tagsinput
 * TODO change to twitter tagsinput, bootstrap tagsinput is depricated
 */
class DropdownMultilinkField extends DropdownLinkField implements AssociationFieldInterface
{
    const DEFAULT_ASSOCIATION_TYPE = ClassMetadataInfo::MANY_TO_MANY;
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

    public function __construct($sqlFieldname, $label, $postName)
    {
        parent::__construct($sqlFieldname, $label, $postName);
        $this->default = new ArrayCollection();
    }


    public function setAllowAdd($isAllowed = true)
    {
        $this->allowAdd = $isAllowed;
        return $this;
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
                    $classname = get_class($value);
                    if ($value instanceof BaseEntity) {
                        $function = $classname::getDefaultGetDisplayStringFunction();
                        $appendString = $function($value);
                    }
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

    /**
     * get the Values of
     * @return ArrayCollection
     */
    private function getValues()
    {
        if (count($this->value) == 0 && !is_null($this->getSourceEntity()->getId())) {
            $modelForIdField = new $this->targetEntity();
            /**
             * @var $model \Entity
             */
            $model =
                BaseEntityRepository::getEntityById(get_class($this->sourceEntity), $this->getSourceEntity()->getId());

            $values = $model->get($this->sourceField);
            $displayStringFunction = $this->getGetDisplayStringFunction();
            if (count($values) > 0 && $values != null) {
                foreach ($model->get($this->sourceField) as $valnum => $value) {
                    $this->value[$value->getId()] = $displayStringFunction($value);
                }
            }
        }

        return $this->value;

    }

    public function getFormView($form, $clientSideValidationActivated = true)
    {
        $html = "<label for='" . $this->getPostName() . "'>" . $this->getLabel() . "</label>";


        $html .= $this->getInputHtml($form, $clientSideValidationActivated);
        return $html;
    }

    /**
     * @param $html
     * @return string
     */
    public function getInputHtml($form, $clientSideValidationActivated = true)
    {
        $values = $this->getValues();
        if (($values == null || count($values) == 0) && count($this->getDefault()) > 0) {
            $values = $this->getDefault();
        }
        if ($values instanceof ArrayCollection || $values instanceof PersistentCollection) {

            $values = $values->toArray();
        } elseif (is_array($values)) {

        } else {

            $values = array();
        }

        $valueStrings = array();

        //to display the values, we have to convert them to strings with our getDisplayString function
        $displayFunction = $this->getDisplayString;
        foreach ($values as $num => $entity) {
            if ($entity instanceof Proxy) {
                $entity->__load();
            }

            $valueStrings[] = $displayFunction($entity);
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
                            limit:0,
                          },{
                           name: '" . $this->getPostName() . "',
                            source: function (q, sync) {
                                  if (q === '' ||q === '*' ) {
                                    sync(values.get($sourcetext));
                                  }
                                
                                  else {
                                    values.search(q, sync);
                                }
                               },
                          }]
                        });
                        
					});
				</script>
				";


        $html .= $this->getHtmlErrorMsg();
        return $html;
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

                if ($findItem != null) {
                    $this->getEntityManager()->persist($findItem);

                    $sqlArray->add($findItem);
                    if (strlen($this->targetField) > 0) {
                        if ($this->associationType == ClassMetadataInfo::ONE_TO_MANY) {
                            $findItem->set($this->targetField, $this->sourceEntity);
                        }
                    }
                }
            } else {
                //TODO throw exception, if invalid values should produce an error message
            }
        }
        $this->setSQLValue($sqlArray);

        return true;
    }

    public function setSQLValue($value)
    {
        if (count($value) == 0) {
            $this->value = new ArrayCollection();
        } elseif ($value instanceof ArrayCollection || $value instanceof PersistentCollection) {
            //check if values are of the right entitiy
            foreach ($value->toArray() as $valnum => $valueitem) {
                if (!$valueitem instanceof $this->targetEntity) {
                    throw new InvalidArgumentException("Item number $valnum is " . get_class($valueitem)
                                                       . ", should be " . $this->targetEntity . " sein");
                }

                //s$this->em->persist($valueitem);
            }
            $this->value = $value;
        } elseif (is_array($value)) {
            $arrayColl = new ArrayCollection();
            foreach ($value as $rownum => $row) {
                if (is_object($row)) {
                    if ($row instanceof $this->targetEntity) {
                        $arrayColl->add($row);
                    } elseif ($row instanceof \stdClass) {
                        $newitem = new $this->targetEntity();
                        foreach ($row as $key => $value) {
                            $newitem->set($key, $value);
                        }
                        $arrayColl->add($newitem);
                    } else {
                        throw new InvalidArgumentException("Item number $rownum is " . get_class($row) . ", should be "
                                                           . $this->targetEntity . " or \stdClass sein");

                    }
                } elseif (is_array($row)) {
                    $newitem = new $this->targetEntity();
                    foreach ($row as $key => $value) {
                        $newitem->set($key, $value);
                    }
                    $arrayColl->add($newitem);
                }
            }
            $this->value = $arrayColl;
        }
        return $this;


    }


}
