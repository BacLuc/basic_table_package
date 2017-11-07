<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 03.10.16
 * Time: 13:08
 */

namespace Concrete\Package\BasicTablePackage\Src;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * Class ExampleEntity
 * @IgnoreAnnotation("package")\n*  Concrete\Package\BasicTablePackage\Src
 * @Entity
 * @Table(name="basic_table_association_cache")
 */
class AssociationCache extends BaseEntity
{
    use EntityGetterSetter;
    /**
     * @var int
     * @Id @Column(type="integer")
     * @GEneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $association_from;


    /**
     * @var string
     * @Column(type="string")
     */
    protected $association_to;


    public function __construct()
    {
        parent::__construct();
    }

    public static function saveAssociationsOfModel(BaseEntity $model)
    {

    }


}