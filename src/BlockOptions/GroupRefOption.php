<?php

namespace Concrete\Package\BasicTablePackage\Src\BlockOptions;

use Concrete\Package\BasicTablePackage\Src\BaseEntityRepository;
use Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry\DiscriminatorEntry;
use Concrete\Package\BasicTablePackage\Src\EntityGetterSetter;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\PersistentCollection;

/*because of the hack with @DiscriminatorEntry Annotation, all Doctrine Annotations need to be
properly imported*/

/**
 * Class GroupRefOption
 * @IgnoreAnnotation("package")
 * @IgnoreAnnotation("package")\n*  Concrete\Package\BasicTablePackage\Src\BlockOptions
 * @Entity
 * @DiscriminatorEntry(value="Concrete\Package\BasicTablePackage\Src\BlockOptions\GroupRefOption")
 */
class GroupRefOption extends TableBlockOption
{
    use EntityGetterSetter;
    /**
     * @var string
     * @Column(type="string")
     */
    protected $optionType = __CLASS__;
    /**
     * @var ArrayCollection of Group
     * @OneToMany(targetEntity="Concrete\Package\BasicTablePackage\Src\BlockOptions\GroupRefOptionGroup",mappedBy="GroupRefOption")
     */
    protected $GroupAssociations;

    public function __construct()
    {
        $this->GroupAssociations = new ArrayCollection();
        $this->optionType == __CLASS__;
        $this->setDefaultFieldTypes();
    }

    public function getLabel()
    {
        return t('Which groups are used in this block?');
    }

    public function getFieldType()
    {
        if ($this->fieldTypes['optionValue'] == null) {
            $this->setDefaultFieldTypes();
        }
        if ($this->optionName != null) {
            $this->fieldTypes['GroupAssociations']->setLabel($this->optionName);
            $this->fieldTypes['GroupAssociations']->setPostName(str_replace(" ", "", $this->optionName));
        }
        return $this->fieldTypes['GroupAssociations'];
    }

    public function getValue()
    {
        if ($this->GroupAssociations instanceof PersistentCollection) {
            $this->GroupAssociations = new ArrayCollection($this->GroupAssociations->toArray());
        }
        return $this->GroupAssociations;
    }

    public function setValue($GroupAssociations)
    {
        if ($GroupAssociations instanceof PersistentCollection) {
            $GroupAssociations = new ArrayCollection($GroupAssociations->toArray());
        }
        if ($GroupAssociations instanceof Entity) {

            $idfieldname = $GroupAssociations->getIdFieldName();
            $optionValue = BaseEntityRepository::getEntityById(
                $GroupAssociations::getFullClassName()
                , $GroupAssociations->$idfieldname
            );

        } elseif ($GroupAssociations instanceof ArrayCollection) {

            foreach ($this->GroupAssociations->toArray() as $key => $value) {
                $this->GroupAssociations->removeElement($value);
                $this->getEntityManager()->remove($value);
            }
            foreach ($GroupAssociations->toArray() as $key => $value) {
                $idfieldname = $value->getIdFieldName();
                $this->getEntityManager()->persist($value);
                $this->GroupAssociations->add($value);
            }

        }


    }
}