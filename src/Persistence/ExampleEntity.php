<?php


namespace BasicTablePackage\Persistence;

use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * Class ExampleEntity
 * @IgnoreAnnotation("package")
 *  Concrete\Package\BasicTablePackage\Src
 * @Entity
 * @Table(name="btExampleEntity")
 */
class ExampleEntity
{
    /**
     * @var int
     * @Id @Column(type="integer")
     * @GEneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Column(type="string")
     */
    private $value;

    public function __get ($name)
    {
        return $this->{$name};
    }


}