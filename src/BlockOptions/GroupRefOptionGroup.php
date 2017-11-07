<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 26.07.16
 * Time: 08:21
 */

namespace Concrete\Package\BasicTablePackage\Src\BlockOptions;


use Concrete\Package\BasicTablePackage\Src\AssociationBaseEntity;
use Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry\DiscriminatorEntry;
use Concrete\Package\BasicTablePackage\Src\EntityGetterSetter;
use Concrete\Package\BasicTablePackage\Src\Group;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\Table;

/*because of the hack with @DiscriminatorEntry Annotation, all Doctrine Annotations need to be
properly imported*/

/**
 * Class
 *  Concrete\Package\BasicTablePackage\Src\BlockOptions
 * @Entity
 * @Table(name="grouprefoption_group")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorEntry(value="Concrete\Package\BasicTablePackage\Src\BlockOptions\GroupRefOptionGroup")
 *
 */
class GroupRefOptionGroup extends AssociationBaseEntity
{
    use EntityGetterSetter;

    /**
     * @var int
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     *
     * @var GroupRefOption
     * @ManyToOne(targetEntity="GroupRefOption", inversedBy="GroupAssociations")
     * @JoinColumn(name="grouprefoption_id", referencedColumnName="id",onDelete="CASCADE")
     */
    protected $GroupRefOption;

    /**
     *
     * @var Group
     * @ManyToOne(targetEntity="Concrete\Package\BasicTablePackage\Src\Group")
     * @JoinColumn(name="group_id", referencedColumnName="gID",onDelete="CASCADE")
     */
    protected $Group;


    public function __construct()
    {
        parent::__construct();
    }


}