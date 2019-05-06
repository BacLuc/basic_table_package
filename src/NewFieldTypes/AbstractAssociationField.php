<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 02.05.17
 * Time: 11:12
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes;


use Concrete\Package\BasicTablePackage\Controller;
use Concrete\Package\BasicTablePackage\Src\BaseEntity;
use Concrete\Package\BasicTablePackage\Src\BaseEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

abstract class AbstractAssociationField extends AbstractField implements AssociationFieldInterface
{
    const DEFAULT_ASSOCIATION_TYPE = ClassMetadataInfo::MANY_TO_ONE;
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
    /**
     * @var array
     */
    protected $options;
    protected $associationType;

    //TODO check if $callable's first parameter is of class Entity

    /**
     * @param DropdownLinkField $source
     * @param DropdownLinkField $target
     */
    public static function copyLinkInfo(AssociationFieldInterface $source, AssociationFieldInterface &$target)
    {
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

    public function setGetDisplayString(callable $getDisplayString)
    {
        $this->getDisplayString = $getDisplayString;
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

    public function setOptions($options)
    {
        $this->options = $options;
        $this->validator->setOptions($options);
        $this->editRepresentation->setOptions($options);
        $this->viewRepresentation->setOptions($options);
    }

    public function getOptionsModelList()
    {


        $queryBuilder = BaseEntityRepository::getBuildQueryWithJoinedAssociations($this->getTargetEntity());


        if ($this->getAssociationType() == ClassMetadataInfo::ONE_TO_ONE
            || $this->getAssociationType() == ClassMetadataInfo::ONE_TO_MANY) {
            //if it is a one to one relation, we have to remove the target entities which already have a relation to another source entity
            $queryConfig = BaseEntityRepository::getQueryConfigOf($this->getTargetEntity());
            $targetEntityAlias = $queryConfig['fromEntityStart']['shortname'];

            $subquery = Controller::getEntityManagerStatic()->createQueryBuilder();

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

    public function getTargetEntity()
    {
        return $this->targetEntity;
    }

    /**
     * @return mixed
     */
    public function getAssociationType()
    {
        return $this->associationType;
    }

    /**
     * @param mixed $associationType
     * @return AbstractAssociationField
     */
    public function setAssociationType($associationType)
    {
        $this->associationType = $associationType;
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

    public function getFullOptions()
    {
        /**
         * @var $em EntityManager
         */
        $em = BaseEntity::getEntityManagerStatic();


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

    public function getTargetField()
    {
        return $this->targetField;
    }


}