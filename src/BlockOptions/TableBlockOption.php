<?php

namespace Concrete\Package\BasicTablePackage\Src\BlockOptions;

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete\Package\BasicTablePackage\Src\BaseEntity;
use Concrete\Package\BasicTablePackage\Src\BlockOptions\CanEditOption;
use Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry\DiscriminatorEntry;
use Concrete\Package\BasicTablePackage\Src\EntityGetterSetter;
use Concrete\Package\BasicTablePackage\Src\Exceptions\InvalidBlockOptionException;
use Concrete\Package\BasicTablePackage\Src\Exceptions\InvalidBlockOptionSetOrderException;
use Concrete\Package\BasicTablePackage\Src\Exceptions\InvalidBlockOptionValueException;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\Table;
use OpenCloud\Common\Log\Logger;

/*because of the hack with @DiscriminatorEntry Annotation, all Doctrine Annotations need to be
properly imported*/

/**
 * Abstract Entitty TableBlockOption that provides the link to BasicTableInstance for the subBlockOptions
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorEntry(value="Concrete\Package\BasicTablePackage\Src\BlockOptions\TableBlockOption")
 * @Table(name="TableBlockOption")
 */
class TableBlockOption extends BaseEntity
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
    protected $optionType;

    /**
     * @var string
     * @Column(type="string",nullable=true)
     */
    protected $optionValue;

    /**
     * @ManyToOne(targetEntity="Concrete\Package\BasicTablePackage\Src\BasicTableInstance", inversedBy="tableBlockOptions")
     * @JoinColumn(name="bID", referencedColumnName="bID", nullable=false, onDelete="CASCADE")
     **/
    protected $BasicTableInstance;


    protected $possibleValues = null;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $optionName = null;

    /**
     *
     */
    public function __construct()
    {
        $this->setDefaultFieldTypes();
        $this->optionType = self::getFullClassName();
    }


    public function set($name, $value)
    {
        //to check the value, optionType and optionValue have to be set

        //\Log::debug(var_dump($this->optionTypes));

        //check if OptionType is valid
        if ($name == 'optionType') {

            if (isset($this->optionValue)) {
                if (!(new $value())->checkValue($this->optionValue)) {
                    $this->optionValue = null;
                }
            }

        }

        //check if option value is valid
        if ($name == 'optionValue') {
            if (!isset($this->optionType)) {
                throw new InvalidBlockOptionSetOrderException("You cannot set the option value without setting the option type");
            }
            if (!(new $this->optionType())->checkValue($value)) {
                throw new InvalidBlockOptionValueException("The value $value is invalid for TableBlockOption "
                                                           . $this->optionType);
            }
        }
        parent::set($name, $value);


        if ($name == 'optionName') {
            $this->getFieldType()->setLabel($value);
        }

    }

    /**
     * @return Concrete\Package\BasicTablePackage\Src\FieldTypes\Field
     */
    public function getFieldType()
    {
        if ($this->fieldTypes['optionValue'] == null) {
            $this->setDefaultFieldTypes();
        }
        if ($this->optionName != null) {
            $this->fieldTypes['optionValue']->setLabel($this->optionName);
            $this->fieldTypes['optionValue']->setPostName(str_replace(" ", "", $this->optionName));
        }
        return $this->fieldTypes['optionValue'];
    }

    public function getPossibleValues()
    {
        return $this->possibleValues;
    }

    public function setPossibleValues($possibleValues)
    {
        $this->possibleValues = $possibleValues;
        //$this->fieldTypes['optionValue'] = new //when the values are restricted, use a dropdown
    }

    public function setValue($value)
    {
        $this->optionValue = $value;
    }

    public function getFormView($form)
    {

        $this->getFieldType()->setSQLValue($this->getValue());
        return $this->getFieldType()->getFormView($form);
    }

    public function getValue()
    {
        return $this->optionValue;
    }

    protected function checkValue($value)
    {
        if ($this->possibleValues != null) {
            //option has to meet
            return isset($this->possibleValues[$value]);
        } else {
            //free option
            return true;
        }
    }
}