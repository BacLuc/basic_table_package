<?php

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;

use Concrete\Package\BasicTablePackage\Src\BaseEntity;
use Concrete\Package\BasicTablePackage\Src\BaseEntityRepository;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DropdownField as DropdownField;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Class DropdownLinkField
 * @IgnoreAnnotation("package")
 *  Concrete\Package\BasicTablePackage\Src\FieldTypes
 * A Dropdownfield which lists other instances
 */
class DropdownLinkField extends DropdownField implements AssociationFieldInterface
{
    const DEFAULT_ASSOCIATION_TYPE = ClassMetadataInfo::MANY_TO_ONE;
    protected $linktable;
    protected $sqlfilter = " 1=1 ";
    protected $sqlvars   = array();
    protected $showcolumn;
    protected $idField   = 'id';
    /**
     * @var BaseEntity
     */
    protected $sourceEntity;
    /**
     * @var String
     */
    protected $sourceField;
    /**
     * @var string
     */
    protected $targetEntity;
    /**
     * @var callable
     */
    protected $getDisplayString;
    /**
     * @var string
     */
    protected $targetField;
    /**
     * @var callable
     */
    protected $filter;
    protected $isBidirectional = false;
    protected $associationType;


    //TODO check if $callable's first parameter is of class Entity

    /**
     * @param DropdownLinkField $source
     * @param DropdownLinkField $target
     */
    public static function copyLinkInfo(/*DropdownLinkField */
        $source, /*DropdownLinkField */
        &$target
    ) {
        $target->setLinkInfo(
            $source->getSourceEntity()
            , $source->getSourceField()
            , $source->getTargetEntity()
            , $source->getTargetField()
            , $source->getAssociationType()
            , $source->getGetDisplayStringFunction()
            , $source->getFilter()
        );
    }

    /**
     * set the Info for the Link table
     * @param BaseEntity $sourceEntity
     * @param $sourceField
     * @param $targetEntity
     * @param null $targetField
     * @param null $associationType
     * @param callable|null $getDisplayString to overrride the default getDisplayStringFunction provided by the Entity
     * @param array|callable|null $filter
     * @return $this
     */
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
        $this->targetEntity = $targetEntity;
        $this->targetField = $targetField;

        if ($associationType == null) {
            $this->associationType = static::DEFAULT_ASSOCIATION_TYPE;
        } else {
            $this->associationType = $associationType;
        }


        $this->getDisplayString = $getDisplayString;
        if ($this->getDisplayString == null) {
            $this->getDisplayString = $targetEntity::getDefaultgetDisplayStringFunction();
        }
        $this->filter = $filter;
        return $this;
    }

    public function getSourceEntity()
    {
        return $this->sourceEntity;
    }

    public function getSourceField()
    {
        return $this->sourceField;
    }

    public function getTargetEntity()
    {
        return $this->targetEntity;
    }

    public function getTargetField()
    {
        return $this->targetField;
    }

    public function getAssociationType()
    {
        return $this->associationType;
    }

    public function setAssociationType($type)
    {
        $this->associationType = $type;
        return $this;
    }

    public function getGetDisplayStringFunction()
    {
        return $this->getDisplayString;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function setFilter(callable $filter)
    {
        $this->filter = $filter;
        return $this;
    }

    public function setGetDisplayString(callable $getDisplayString)
    {
        $this->getDisplayString = $getDisplayString;
        return $this;
    }

    public function getIsBidirectional()
    {
        return $this->isBidirectional;
    }

    public function setIsBidirectional($bool)
    {
        $this->isBidirectional = $bool;
        return $this;
    }

    public function getTableView()
    {
        $value = $this->getSQLValue();
        if (is_null($value)) {
            return "";
        }
        /**
         * @var callable $displayStringFunction
         */
        $displayStringFunction = $this->getDisplayString;
        return $displayStringFunction($value);
    }

    /**
     * @return BaseEntity
     */
    public function getSQLValue()
    {
        return $this->value;

    }

    public function getFullOptions()
    {
        /**
         * @var $em EntityManager
         */
        $em = $this->getEntityManager();


        $modelList = $this->getOptionsModelList();

        $options = array();
        if (count($modelList) > 0) {

            /**
             * @var BaseEntity $model
             */
            foreach ($modelList as $model) {
                $model->setDefaultFieldTypes();
                $options[$model->getId()] = $model->toTableAssoc();
                $displayStringFunction = $this->getDisplayString;
                $options[$model->getId()]->uniqueIdString = $displayStringFunction($model);

            }
        }


        return $options;
    }

    /**
     * @return array
     */
    public function getOptionsModelList()
    {
        $em = $this->getEntityManager();


        $queryBuilder = BaseEntityRepository::getBuildQueryWithJoinedAssociations($this->getTargetEntity());


        if ($this->getAssociationType() == ClassMetadataInfo::ONE_TO_ONE
            || $this->getAssociationType() == ClassMetadataInfo::ONE_TO_MANY) {
            //if it is a one to one relation, we have to remove the target entities which already have a relation to another source entity
            $queryConfig = BaseEntityRepository::getQueryConfigOf($this->getTargetEntity());
            $targetEntityAlias = $queryConfig['fromEntityStart']['shortname'];

            $subquery = $this->getEntityManager()->createQueryBuilder();

            $subquery->select("sub0." . $this->getSourceEntity()->getIdFieldName())
                     ->from(get_class($this->getSourceEntity()), "sub0")
                     ->leftJoin("sub0." . $this->getSourceField(), "sub1")
                     ->where($subquery->expr()->eq("sub1", $targetEntityAlias))
            ;

            //if sourceentity already exists, its value can be in the options too
            if ($this->getSourceEntity()->getId() != null) {
                $subquery
                    ->andWhere($subquery->expr()->neq("sub0." . $this->getSourceEntity()->getIdFieldName(),
                        ":sourceEntityId"));
                $queryBuilder->setParameter(":sourceEntityId", $this->getSourceEntity()->getId());
            }
            $queryBuilder->andWhere(
                $queryBuilder->expr()->not($queryBuilder->expr()->exists(
                    $subquery
                )
                )
            );
        }

        $modelList = $queryBuilder->getQuery()->getResult();
        return $modelList;
    }

    public function validatePost($value)
    {

        $values = array_keys($this->getOptions());
        if (count($values) == 0) {
            return true;
        }
        if (in_array($value, $values)) {
            $modelForIdField = new $this->targetEntity();

            //TODO check if is to refactor
            $model = BaseEntityRepository::getEntityById($this->targetEntity, $value);

            if ($model != null && $model != false) {
                $this->getEntityManager()->persist($model);
                $this->setSQLValue($model);
            } else {
                $this->setSQLValue(null);
            }

        } else {
            if ($this->nullable !== false && $value == null) {
                return true;
            }
            return false;
        }
        //because of directeditfield references of referenced object could have been changed, persist them too


        return true;
    }

    /**
     * @return array of options
     */
    public function getOptions()
    {
        /**
         * @var $em EntityManager
         */
        $modelList = $this->getOptionsModelList();

        $options = array();
        if (count($this->options) == 0) {
            if (count($modelList) > 0) {

                foreach ($modelList as $model) {
                    if ($this->getDisplayString != null) {
                        $displayFunction = $this->getDisplayString;
                        $options[$model->getId()] = trim($displayFunction($model));
                    }
                }
            }
        } else {
            return $this->options;
        }

        if ($this->getNullable() !== false) {
            $options = array("" => "") + $options;
        }
        $this->setOptions($options);
        return $this->options;


    }

    public function setSQLValue($value)
    {
        if ($value instanceof $this->targetEntity || is_null($value)) {
            $this->value = $value;
        } else {
            throw new InvalidArgumentException("Parameter \$value is " . get_class($value) . ", should be "
                                               . $this->targetEntity . " ");
        }
        return $this;
    }

    public function getInputHtml($form, $clientSideValidationActivated = true)
    {
        $value = $this->getSQLValue();
        $default = $this->getDefault();
        if ($value == null && $default != null) {
            $value = $default;
        }
        if ($this->value != null) {
            $setValue = $value->getId();
        } else {

            $setValue = "";

        }
        $html = static::select($this->getHtmlId(), $this->getPostName(), $this->getOptions(), $setValue);

        $html .= $this->getHtmlErrorMsg();
        return $html;
    }


}