<?php


namespace BasicTablePackage\Test\Entity;

use BasicTablePackage\Lib\GetterTrait;
use BasicTablePackage\Lib\SetterTrait;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * Class ExampleEntity
 * @IgnoreAnnotation("package")
 *  Concrete\Package\BasicTablePackage\Src
 * @Entity
 * @Table(name="btExampleEntity")
 */
class SomeEntity
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

    /**
     * @Column(type="date", nullable=true)
     */
    private $datecolumn;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $datetimecolumn;

    /**
     * @Column(type="text")
     */
    private $wysiwygcolumn;


    /**
     * @var SomeReferencedEntity
     * @ManyToOne(targetEntity="BasicTablePackage\Test\Entity\SomeReferencedEntity")
     */
    protected $manyToOne;

    /**
     * @var SomeReferencedEntity[]
     * @ManyToMany(targetEntity="BasicTablePackage\Test\Entity\SomeReferencedEntity" )
     */
    protected $manyToMany;
}