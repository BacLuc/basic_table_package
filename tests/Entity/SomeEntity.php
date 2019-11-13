<?php


namespace BasicTablePackage\Test\Entity;

use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
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

    public function __get($name)
    {
        return $this->{$name};
    }

    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }
}