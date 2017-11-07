<?php

namespace Concrete\Package\BasicTablePackage\Src\BlockOptions;

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged\Exceptions\InvalidBlockOptionException;
use Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged\Exceptions\InvalidBlockOptionSetOrderException;
use Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged\Exceptions\InvalidBlockOptionValueException;
use Concrete\Package\BasicTablePackage\Src\BlockOptions\CanEditOption;
use Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry\DiscriminatorEntry;
use Concrete\Package\BasicTablePackage\Src\EntityGetterSetter;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use OpenCloud\Common\Log\Logger;

/*because of the hack with @DiscriminatorEntry Annotation, all Doctrine Annotations need to be
properly imported*/

/**
 * Class TextBlockOption
 * @IgnoreAnnotation("package")\n*  Application\Block\BasicTableBlock
 * @Entity
 * @Table(name="TextBlockOptions")
 * @DiscriminatorEntry(value="Concrete\Package\BasicTablePackage\Src\BlockOptions\TextBlockOption")
 */
class TextBlockOption extends TableBlockOption
{
    use EntityGetterSetter;


}