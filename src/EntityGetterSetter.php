<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 20.08.16
 * Time: 10:37
 */

namespace Concrete\Package\BasicTablePackage\Src;

use Concrete\Package\BasicTablePackage\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

/**
 * Class EntityGetterSetter
 * @IgnoreAnnotation("package")\n*  Concrete\Package\BasicTablePackage\Src
 * Because the getter and setter method of Class Entity has only access to their members, and not to the members of the subclasses
 * (where the getters and setters are really needed), one hat to copy the getters and setters to every class.
 * Now, they are extracted to this trait
 */
trait EntityGetterSetter
{


    public function set($name, $value)
    {
        if (property_exists($this, $name)
            && !in_array($name, $this->protect)
            && !in_array($name, $this->protectWrite)
            && !in_array($name, $this->fieldTypes)
        ) {
            if ($this->$name instanceof ArrayCollection || $this->$name instanceof PersistentCollection) {
                $this->$name = $this->mergeCollections($this->$name, $value);
                return $this;
            }

            $this->$name = $value;
            return $this;
        }
    }

    /**
     * @param ArrayCollection|PersistentCollection $coll1
     * @param ArrayCollection|PersistentCollection $coll2
     * @return ArrayCollection;
     */
    public function mergeCollections($coll1, $coll2)
    {
        if ($coll1 == null && $coll2 == null) {
            return new ArrayCollection();
        }
        if ($coll1 instanceof PersistentCollection) {
            $coll1 = new ArrayCollection($coll1->toArray());
        }
        if ($coll2 instanceof PersistentCollection) {
            $coll2 = new ArrayCollection($coll2->toArray());
        }

        if ($coll1 == null) {
            return $coll2;
        }

        if ($coll2 == null) {
            return $coll1;
        }
        /**
         * @var ArrayCollection $coll1
         */
        /**
         * @var ArrayCollection $coll2 ;
         */
        $result = new ArrayCollection();
        foreach ($coll2->toArray() as $key => $value) {
            //wenn element in beiden Arrays
            if (!$result->contains($value)) {
                $result->add($value);
            }
        }

        //now delete not anymore existent elements
        foreach ($coll1->toArray() as $key => $value) {
            if (!$result->contains($value)) {
                if ($value instanceof AssociationBaseEntity) {
                    Controller::getEntityManagerStatic()->remove($value);
                }
            }
        }

        return $result;

    }

    public function __get($name)
    {
        $returnvar = $this->get($name);
        return $returnvar;
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function get($name)
    {
        if (property_exists($this, $name)
            && !in_array($name, $this->protect)
            && !in_array($name, $this->protectRead)
            && !in_array($name, $this->fieldTypes)) {

            $returnvar = $this->{$name};


            return $returnvar;
        }
    }
}