<?php


namespace BaclucC5Crud\Entity;

use BaclucC5Crud\Lib\GetterTrait;
use BaclucC5Crud\Lib\SetterTrait;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * Class ExampleEntity
 * @IgnoreAnnotation("package")
 *  Concrete\Package\BaclucC5Crud\Src
 * @Entity
 * @Table(name="btReferencedEntity")
 */
class ReferencedEntity implements WithUniqueStringRepresentation
{
    use GetterTrait, SetterTrait;
    /**
     * @var int
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     * @Column(type="string")
     */
    private $value;

    /**
     * @Column(type="integer", nullable=true)
     */
    protected $intcolumn;


    public function createUniqueString(): string
    {
        return $this->id . " " . $this->value . " " . $this->intcolumn;
    }

    public function __toString()
    {
        return $this->createUniqueString();
    }


}