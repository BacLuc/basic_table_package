<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 07.12.15
 * Time: 22:01
 */

namespace Concrete\Package\BasicTablePackage\Src;

use Concrete\Package\BasicTablePackage\Src\BlockOptions\TableBlockOption;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/*because of the hack with @DiscriminatorEntry Annotation, all Doctrine Annotations need to be
properly imported*/

/**
 * Class BasicTableInstance
 * Just an Id to connect the Block with TableBlockOptions
 * Concrete\Package\BasicTablePackage\Src
 * @Entity
 * @Table(name="btBasicTableInstance")
 */
class BasicTableInstance extends BaseEntity
{
    use EntityGetterSetter;
    /**
     * @var int
     * @Id @Column(type="integer")
     */
    protected $bID;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Concrete\Package\BasicTablePackage\Src\BlockOptions\TableBlockOption", mappedBy="BasicTableInstance")
     **/
    protected $tableBlockOptions;

    public function __construct()
    {
        $this->tableBlockOptions = new ArrayCollection();
        $this->fieldTypes['bID'] = new FieldTypes\Field('bID', 'bID', 'identifier');
        $this->fieldTypes['tableBlockOptions'] = new FieldTypes\Field('value', 'Value', 'value');

    }

    public function addBlockOption(TableBlockOption $option)
    {
        $this->tableBlockOptions[] = $option;
    }

    public function removeBlockOption(TableBlockOption $option)
    {
        $this->tableBlockOptions->removeElement($option);
    }


    public function getId()
    {
        return $this->bID;

    }

    public function getIdFieldName()
    {
        return "bID";
    }


}