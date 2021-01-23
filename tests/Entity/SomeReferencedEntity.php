<?php

namespace BaclucC5Crud\Test\Entity;

use BaclucC5Crud\Entity\Identifiable;
use BaclucC5Crud\Lib\GetterTrait;
use BaclucC5Crud\Lib\SetterTrait;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * Class ExampleEntity.
 *
 * @IgnoreAnnotation("package")
 *  Concrete\Package\BaclucC5Crud\Src
 * @Entity
 * @Table(name="btSomeReferencedEntity")
 */
class SomeReferencedEntity implements Identifiable {
    use GetterTrait;
    use SetterTrait;

    /**
     * @Column(type="integer", nullable=true)
     */
    protected $intcolumn;

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

    public function getId() {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public static function getIdFieldName(): string {
        return 'id';
    }
}
