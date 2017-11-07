<?php

namespace Concrete\Package\BasicTablePackage\Src\BlockOptions;

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged\Exceptions\InvalidBlockOptionException;
use Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged\Exceptions\InvalidBlockOptionSetOrderException;
use Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged\Exceptions\InvalidBlockOptionValueException;
use Concrete\Package\BasicTablePackage\Src\BlockOptions\CanEditOption;
use Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry\DiscriminatorEntry;
use Concrete\Package\BasicTablePackage\Src\EntityGetterSetter;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DropdownField;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use OpenCloud\Common\Log\Logger;

/*because of the hack with @DiscriminatorEntry Annotation, all Doctrine Annotations need to be
properly imported*/

/**
 * Class TextBlockOption
 * @IgnoreAnnotation("package")\n*  Application\Block\BasicTableBlock
 * @Entity
 * @Table(name="DropDownBlockOptions")
 * @DiscriminatorEntry(value="Concrete\Package\BasicTablePackage\Src\BlockOptions\DropdownBlockOption")
 */
class DropdownBlockOption extends TableBlockOption
{

    use EntityGetterSetter;


    protected $possibleValues = null;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

    }

    public function setDefaultFieldTypes()
    {
        $field = new DropdownField("optionValue", t('Value'), t('postValue'));
        $this->setControllerFieldType("optionValue", $field);
    }

    public function setPossibleValues($possibleValues)
    {
        parent::setPossibleValues($possibleValues);
        $this->getFieldType()->setOptions($this->possibleValues);
    }


}