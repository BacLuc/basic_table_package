<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 02.05.17
 * Time: 13:46
 */

namespace Concrete\Package\BasicTablePackage\Src;


use Concrete\Package\BasicTablePackage\Controller;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Proxy\Proxy;
use Doctrine\ORM\QueryBuilder;

class BaseEntityRepository
{

    public static function getBaseEntityFromProxy(BaseEntity $item)
    {
        if (!($item instanceof Proxy)) {
            return $item;
        }
        return static::getEntityById(get_class($item), $item->getId());

    }

    /**
     * @param $classname
     * @param $id
     * @param callable|null $addFilterFunction
     * @return BaseEntity
     */
    public static function getEntityById($classname, $id, callable $addFilterFunction = null)
    {
        $queryBuilder = static::getBuildQueryWithJoinedAssociations($classname, $addFilterFunction);
        $queryConfig = static::getQueryConfigOf($classname);
        $instanceForId = new $classname();
        $idFieldName = $instanceForId->getIdFieldName();
        $queryBuilder->andWhere(
            $queryBuilder->expr()->eq($queryConfig['fromEntityStart']['shortname'] . "." . $idFieldName,
                ":getEntityById")
        );
        $queryBuilder->setParameter(":getEntityById", $id);
        $models = $queryBuilder->getQuery()->getResult();
        if ($models != null) {
            return $models[0];
        }
        return null;

    }

    /**
     * @param null $classname if no classname is passed, the static classname is used
     * @param callable|null $addFilterFunction
     * if $addFilterFunciton is BaseEntity::$staticEntityfilterfunction, then
     * the function gets switched to  $addFilterFunction = static::$staticEntityfilterfunction;
     * null : no filter
     * if you really want to use the filter of BaseEntity, use:
     * BaseEntity::$baseEntityfilterFunction
     *
     * @return QueryBuilder
     *
     * $addFilterFunction must be of signature and is called in this function:
     * /**
     * @param QueryBuilder $query
     * @param array $queryConfig
     *  array of:
     * array(
     * 'fromEntityStart' => array('shortname'=> 'e0'
     *                                                       , 'class'=>get_class($this->model)
     *                                             )
     *       ,'firstAssociationFieldname'=> array('shortname' => 'e1'
     *                                                                           , 'class' => 'Namespace\To\Entity\Classname')
     *
     * );
     * @return QueryBuilder
     *
     */
    public static function getBuildQueryWithJoinedAssociations($classname, callable $addFilterFunction = null)
    {

        if ($classname == null) {
            throw new InvalidArgumentException("the first parameter cannot be null");
        }

        //if the staticEntityfilterfunction was passed, then use the staticEntityfilterFunciton of the Entity called
        if ($addFilterFunction == BaseEntity::$staticEntityfilterfunction) {
            $fullclassname = $classname;
            if ($classname[0] != '\\') {
                $fullclassname = '\\' . $classname;
            }
            $addFilterFunction = $fullclassname::$staticEntityfilterfunction;
        }


        //TODO check if addFilterFunciton has the right signature

        //
        $selectEntities = static::getSelectEntitiesOf($classname);


        /**
         * @var QueryBuilder $query
         */
        $query = Controller::getEntityManagerStatic()->createQueryBuilder();

        //add select

        $entities = $selectEntities;

        $selectString = "";
        $entityCounter = 0;
        foreach ($entities as $fieldname => $entityName) {
            if ($entityCounter > 0) {
                $selectString .= ",";
            }
            $selectString .= " e" . $entityCounter++;
        }
        $query->select($selectString);


        $query->from(reset(array_keys($entities)), "e0");

        $entityCounter = 1;
        $first = true;
        /**
         * build an array with
         * array(sqlfieldname => array('shortname'=> e1, 'class'=> classname))
         * first entry is fromEntityStart
         */
        foreach ($selectEntities as $fieldName => $entityName) {
            if ($first) {

                //first entity is the from clause, so no join required
                $first = false;
                continue;
            }
            $query->leftJoin("e0." . $fieldName, "e" . $entityCounter++);

        }

        $queryConfig = static::getQueryConfigOf($classname);


        if ($addFilterFunction != null) {
            $query = $addFilterFunction($query, $queryConfig);
        }

        return $query;
    }


    /**
     * @param $classname
     * @return array of the associated entities with $classname, or static::getFullClassName if $classname is null
     * is in form of:
     * array(
     * Namespace\To\StartEntity\Classname => null,
     * 'associationfieldname' => Namespace\To\Association\Classname
     * );
     */
    public static function getSelectEntitiesOf($classname)
    {

        $selectEntities = array($classname => null);

        /**
         * @var ClassMetadata $metadata
         */
        $metadata = Controller::getEntityManagerStatic()->getMetadataFactory()->getMetadataFor($classname);
        foreach ($metadata->getAssociationMappings() as $mappingnum => $mapping) {
            $targetEntityInstance = new $mapping['targetEntity']();
            $selectEntities[$mapping['fieldName']] = $mapping['targetEntity'];

        }

        return $selectEntities;

    }

    /**
     * @param string $classname
     * @return array
     * array(
     * 'fromEntityStart' => array('shortname'=> 'e0'
     *                                                       , 'class'=>get_class($this->model)
     *                                             )
     *       ,'firstAssociationFieldname'=> array('shortname' => 'e1'
     *                                                                           , 'class' => 'Namespace\To\Entity\Classname')
     *
     * );
     */
    public static function getQueryConfigOf($classname)
    {
        if ($classname == null) {
            throw new \InvalidArgumentException("the parameter cannot be null");
        }

        $selectEntities = static::getSelectEntitiesOf($classname);
        $first = true;
        $entityCounter = 1;
        foreach ($selectEntities as $fieldName => $entityName) {
            if ($first) {
                $queryConfig['fromEntityStart'] = array(
                    'shortname' => "e0"
                    ,
                    'class'     => $classname,
                );
                //first entity is the from clause, so no join required
                $first = false;
                continue;
            } else {
                $queryConfig[$fieldName] = array(
                    'shortname' => "e" . $entityCounter
                    ,
                    'class'     => $entityName,
                );
            }

        }

        return $queryConfig;

    }

    /**
     * @param string|object $classname or object where the constants need to be extracted
     * @param string $constant_starts_with First part of the constant (if there are multiple constanted options)
     * @return array
     */
    public static function getOptionsOfConstants($classname, $constant_starts_with)
    {
        $refl = new \ReflectionClass($classname);
        $constants = $refl->getConstants();
        $userConstants = array();
        foreach ($constants as $key => $value) {
            if (mb_strpos($value, $constant_starts_with) !== null) {
                $userConstants[$value] = $value;
            }
        }
        return $userConstants;
    }

//
//    public static function getBaseEntityFromProxy(BaseEntity $item)
//    {
//        if (!($item instanceof Proxy)) {
//            return $item;
//        }
//        //to get em, we need package first
//        $pkg = Package::getByHandle("basic_table_package");
//        $em = $pkg->getEntityManager();
//        return DateField::getEntityById(get_class($item), $item->getId());
//
//    }
//
//    /**
//     * @param string|object $classname or object where the constants need to be extracted
//     * @param string $constant_starts_with First part of the constant (if there are multiple constanted options)
//     * @return array
//     */
//    public static function getOptionsOfConstants($classname, $constant_starts_with)
//    {
//        $refl = new \ReflectionClass($classname);
//        $constants = $refl->getConstants();
//        $userConstants = array();
//        foreach ($constants as $key => $value) {
//            if (mb_strpos($value, $constant_starts_with) !== null) {
//                $userConstants[$value] = $value;
//            }
//        }
//        return $userConstants;
//    }
//
//    /**
//     * @param null $classname if no classname is passed, the static classname is used
//     * @param callable|null $addFilterFunction
//     * if $addFilterFunciton is BaseEntity::$staticEntityfilterfunction, then
//     * the function gets switched to  $addFilterFunction = static::$staticEntityfilterfunction;
//     * null : no filter
//     * if you really want to use the filter of BaseEntity, use:
//     * BaseEntity::$baseEntityfilterFunction
//     *
//     * @return QueryBuilder
//     *
//     * $addFilterFunction must be of signature and is called in this function:
//     * /**
//     * @param QueryBuilder $query
//     * @param array $queryConfig
//     *  array of:
//     * array(
//    * 'fromEntityStart' => array('shortname'=> 'e0'
//     *                                                       , 'class'=>get_class($this->model)
//     *                                             )
//     *       ,'firstAssociationFieldname'=> array('shortname' => 'e1'
//     *                                                                           , 'class' => 'Namespace\To\Entity\Classname')
//     *
//     * );
//     * @return QueryBuilder
//
//     *
//     */
//    public static function getBuildQueryWithJoinedAssociations($classname, callable $addFilterFunction = null)
//    {
//
//        if ($classname == null) {
//            throw new InvalidArgumentException("the first parameter cannot be null");
//        }
//
//        //if the staticEntityfilterfunction was passed, then use the staticEntityfilterFunciton of the Entity called
//        if ($addFilterFunction == BaseEntity::$staticEntityfilterfunction) {
//            $fullclassname = $classname;
//            if ($classname[0] != '\\') {
//                $fullclassname = '\\' . $classname;
//            }
//            $addFilterFunction = $fullclassname::$staticEntityfilterfunction;
//        }
//
//
//        //TODO check if addFilterFunciton has the right signature
//
//        //
//        $selectEntities = DateField::getSelectEntitiesOf($classname);
//
//
//        /**
//         * @var QueryBuilder $query
//         */
//        $query = static::getEntityManagerStatic()->createQueryBuilder();
//
//        //add select
//
//        $entities = $selectEntities;
//
//        $selectString = "";
//        $entityCounter = 0;
//        foreach ($entities as $fieldname => $entityName) {
//            if ($entityCounter > 0) {
//                $selectString .= ",";
//            }
//            $selectString .= " e" . $entityCounter++;
//        }
//        $query->select($selectString);
//
//
//        $query->from(reset(array_keys($entities)), "e0");
//
//        $entityCounter = 1;
//        $first = true;
//        /**
//         * build an array with
//         * array(sqlfieldname => array('shortname'=> e1, 'class'=> classname))
//         * first entry is fromEntityStart
//         */
//        foreach ($selectEntities as $fieldName => $entityName) {
//            if ($first) {
//
//                //first entity is the from clause, so no join required
//                $first = false;
//                continue;
//            }
//            $query->leftJoin("e0." . $fieldName, "e" . $entityCounter++);
//
//        }
//
//        $queryConfig = DateField::getQueryConfigOf($classname);
//
//
//        if ($addFilterFunction != null) {
//            $query = $addFilterFunction($query, $queryConfig);
//        }
//
//        return $query;
//    }/**
//     * @param $classname
//     * @return array of the associated entities with $classname, or static::getFullClassName if $classname is null
//     * is in form of:
//     * array(
//        Namespace\To\StartEntity\Classname => null,
//     * 'associationfieldname' => Namespace\To\Association\Classname
//     * );
// */
//    public static function getSelectEntitiesOf($classname)
//    {
//
//        $selectEntities = array($classname => null);
//
//        /**
//         * @var ClassMetadata $metadata
//         */
//        $metadata = static::getEntityManagerStatic()->getMetadataFactory()->getMetadataFor($classname);
//        foreach ($metadata->getAssociationMappings() as $mappingnum => $mapping) {
//            $targetEntityInstance = new $mapping['targetEntity'];
//            $selectEntities[$mapping['fieldName']] = $mapping['targetEntity'];
//
//        }
//
//        return $selectEntities;
//
//    }/**
//     * @param string $classname
//     * @return array
//     * array(
//    'fromEntityStart' => array('shortname'=> 'e0'
//     *                                                       , 'class'=>get_class($this->model)
//     *                                             )
//     *       ,'firstAssociationFieldname'=> array('shortname' => 'e1'
//     *                                                                           , 'class' => 'Namespace\To\Entity\Classname')
//     *
//     * );
// */
//    public static function getQueryConfigOf($classname)
//    {
//        if ($classname == null) {
//            throw new InvalidArgumentException("the parameter cannot be null");
//        }
//
//        $selectEntities = DateField::getSelectEntitiesOf($classname);
//        $first = true;
//        $entityCounter = 1;
//        foreach ($selectEntities as $fieldName => $entityName) {
//            if ($first) {
//                $queryConfig['fromEntityStart'] = array('shortname' => "e0"
//                , 'class' => $classname
//                );
//                //first entity is the from clause, so no join required
//                $first = false;
//                continue;
//            } else {
//                $queryConfig[$fieldName] = array('shortname' => "e" . $entityCounter
//                , 'class' => $entityName
//                );
//            }
//
//        }
//
//        return $queryConfig;
//
//    }/**
//     * @param $classname
//     * @param $id
//     * @param callable|null $addFilterFunction
//     * @return BaseEntity
// */
//    public static function getEntityById($classname, $id, callable $addFilterFunction = null)
//    {
//        $queryBuilder = DateField::getBuildQueryWithJoinedAssociations($classname, $addFilterFunction);
//        $queryConfig = DateField::getQueryConfigOf($classname);
//        $instanceForId = new $classname;
//        $idFieldName = $instanceForId->getIdFieldName();
//        $queryBuilder->andWhere(
//            $queryBuilder->expr()->eq($queryConfig['fromEntityStart']['shortname'] . "." . $idFieldName, ":getEntityById")
//        );
//        $queryBuilder->setParameter(":getEntityById", $id);
//        $models = $queryBuilder->getQuery()->getResult();
//        if ($models != null) {
//            return $models[0];
//        }
//        return null;
//
//    }

}