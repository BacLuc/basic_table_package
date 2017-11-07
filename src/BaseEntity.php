<?php

/**
 * This is a simple helper class that provides visibility to the object
 * properties outside of this class. By default, all properties are readable
 * and writable from outside of the entity class but each property can be
 * protected from both outside actions (read or write) with the $protect,
 * $protectRead and $protectWrite arrays as follows:
 *
 * $protect      - All properties defined within this array are neither writable
 *                 nor readable. They are protected to be used only by this
 *                 entity class.
 * $protectRead  - All properties defined within this array are protected from
 *                 reading outside of this class. It means that they can only
 *                 be read within this entity class.
 * $protectWrite - All properties defined within this array are protected from
 *                 writing outside of this class. It means that they can only
 *                 be written within this entity class.
 *
 * @author Antti Hukkanen <antti.hukkanen(at)mainiotech.fi>
 * @copyright 2014 Mainio Tech Ltd.
 * @license MIT
 */

namespace Concrete\Package\BasicTablePackage\Src;

use Concrete\Core\Package\Package;
use Concrete\Package\BasicTablePackage\Src\BlockOptions\CanEditOption;
use Concrete\Package\BasicTablePackage\Src\Exceptions\ConsistencyCheckException;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DateField as DateField;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\Field;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\FieldTypeListFactory;
use Doctrine\ORM\QueryBuilder;


/**
 * Class Entity
 * @IgnoreAnnotation("package")\n*  Concrete\Package\BasicTablePackage\Src
 * here are the most important methods for entities, so that they don't have to be implemented in every Entity, And that there are no errors if they are not implementet.
 */
abstract class BaseEntity
{
    use EntityGetterSetter;

    public static $staticEntityfilterfunction;
    public static $baseEntityfilterFunction;
    protected     $protect      = array();
    protected     $protectRead  = array();
    protected     $protectWrite = array();
    /**
     * @var Field[]
     */
    protected $fieldTypes          = array();
    protected $em;
    protected $defaultFormView     = false;
    protected $defaultSubFormView  = false;
    protected $checkingConsistency = false;

    public function __construct()
    {
        // $this->setDefaultFieldTypes();
        $this->setDefaultFormViews();
    }

    public function setDefaultFormViews()
    {

    }

    public static function getFullClassName()
    {
        return get_class();
    }

    /**
     * @return \Closure
     * @override
     */
    public static function getDefaultGetDisplayStringFunction()
    {
        $function = function (BaseEntity $item) {
            $returnString = "";
            $metadata = $item->getEntityManager()->getMetadataFactory()->getMetadataFor(get_class($item));
            $fieldTypes = $item->get('fieldTypes');
            foreach ($metadata->getFieldNames() as $fieldnum => $fieldname) {
                try {
                    $mapping = $metadata->getFieldMapping($fieldname);

                    if (isset($fieldTypes[$fieldname])) {
                        $field = $fieldTypes[$fieldname];
                    } else {
                        switch ($mapping['type']) {

                            case 'datetime':
                                $field = new DateField("justlocal", "justlocal", "justlocal");
                                break;
                            default:
                                $field = new Field("justlocal", "justlocal", "justlocal");
                                break;
                        }
                    }
                    $sqlvalue = $item->$fieldname;
                    $field->setSQLValue($sqlvalue);
                    $returnString .= $field->getTableView() . " ";


                } catch (MappingException $e) {
                    //wenn das feld ein association mapping ist, dann gibts error
                    // $this->fieldTypes[$field] = new Field($field, t($field), t("post" . $field));
                }
            }
            return $returnString;
        };
        return $function;
    }

    public function getEntityManager()
    {
        if ($this->em == null) {

            $pkg = Package::getByHandle('basic_table_package');
            $this->em = $pkg->getEntityManager();
            return $this->em;
        } else {
            return $this->em;
        }
    }

    public static function getEntityManagerStatic()
    {
        $pkg = Package::getByHandle("basic_table_package");
        return $pkg->getEntityManager();
    }

    public function setControllerFieldType($name, Field $field)
    {
        if (property_exists($this, $name)
            && !in_array($name, $this->protect)
            && !in_array($name, $this->protectWrite)
            && !in_array($name, $this->fieldTypes)
        ) {
            $this->fieldTypes[$name] = $field;
        }
    }

    public function getAsAssoc()
    {
        $returnArray = array();
        foreach ($this->fieldTypes as $key => $value) {

            $returnArray[$key] = $this->get($key);
        }
        return $returnArray;
    }

    public function getIdFieldName()
    {
        return 'id';
    }

    public function toTableAssoc()
    {
        $jsonObj = new \stdClass();
        if (count($this->fieldTypes) > 0) {
            foreach ($this->fieldTypes as $sqlfieldname => $value) {
                $sqlValue = $this->get($sqlfieldname);
                if ($sqlValue instanceof BaseEntity) {
                    $jsonObj->{$value->getPostname()} = $sqlValue->getId();
                } else {
                    if ($sqlValue === true) {
                        $sqlValue = 1;
                    }
                    if ($sqlValue === false) {
                        $sqlValue = 0;
                    }
                    $jsonObj->{$value->getPostname()} = $value->setSQLValue($sqlValue)->getTableView();
                }
            }
        }
        return $jsonObj;
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getTypeaheadTemplate()
    {

        $template = "<div>{{uniqueIdString}}</div>";

        return $template;
    }

    public function getDefaultSubFormView($form, $clientSideValidationActivated = true)
    {
        if ($this->defaultSubFormView !== false) {
            return $this->defaultSubFormView->getFormView($form, $clientSideValidationActivated);
        }
        return $this->getDefaultFormView($form, $clientSideValidationActivated);

    }

    public function getDefaultFormView($form, $clientSideValidationActivated = true)
    {
        if ($this->defaultFormView !== false) {
            return $this->defaultFormView;
        }
        return false;

    }

    /**
     * Because of possible cycles, checkConsistency Function of every Entity must be a semaphore
     * @return array
     * @throws ConsistencyCheckException
     */
    public function checkConsistency()
    {
        if ($this->checkingConsistency) {
            throw new ConsistencyCheckException("Already checking Consistency of this Entity");
        }
        $this->checkingConsistency = true;
        $this->checkingConsistency = false;
        return array();
    }

    /**
     * @param string $sqlFieldName
     * @param bool $value
     * @throws \InvalidArgumentException if $sqlFieldname does not exist
     */
    public function setFieldTypeIsNotSet($sqlFieldName, $value)
    {
        $fieldTypes = $this->getFieldTypes();
        if (isset($this->fieldTypes[$sqlFieldName])) {
            $this->fieldTypes[$sqlFieldName]->setNotSet($value);
        } else {
            throw new \InvalidArgumentException(sprintf("Property / Fieldtype %s does not exist in class %s",
                $sqlFieldName, static::class));
        }
    }

    /**
     * @return Field[]
     */
    public function getFieldTypes()
    {
        if (count($this->fieldTypes) == 0) {
            $this->setDefaultFieldTypes();

        }
        return $this->fieldTypes;
    }

    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Exception
     */
    public function setDefaultFieldTypes()
    {


        $fieldFactory = new FieldTypeListFactory($this);
        $this->fieldTypes = $fieldFactory->createFieldList();

        if ($this->getId() == null) {
            $this->setDefaultValues();
        }

    }

    /**
     * @return $this
     */
    public function setDefaultValues()
    {
        return $this;
    }

    /**
     * @param string $sqlFieldName
     * @return bool
     * @throws \InvalidArgumentException if $sqlFieldName does not exist
     */
    public function getFieldTypeIsNotSet($sqlFieldName)
    {
        $this->getFieldTypes();
        if (isset($this->fieldTypes[$sqlFieldName])) {
            return $this->fieldTypes[$sqlFieldName]->isNotSet();
        } else {
            throw new \InvalidArgumentException(sprintf("Property / Fieldtype %s does not exist in class %s",
                $sqlFieldName, static::class));
        }
    }


}

/**
 * @param QueryBuilder $query
 * @param array $queryConfig
 * @return QueryBuilder
 * if you want to change the defaultFilterFunction of an Entity, put this statement under your class,
 * but with your classname of course
 */
BaseEntity::$staticEntityfilterfunction = function (QueryBuilder $query, array $queryConfig = array()) {
    return $query;
};

/**
 * @param QueryBuilder $query
 * @param array $queryConfig
 * @return QueryBuilder
 * the filter funciton of base entity
 */
BaseEntity::$baseEntityfilterFunction = function (QueryBuilder $query, array $queryConfig = array()) {
    return $query;
};
