<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.04.17
 * Time: 19:33
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes;


use Concrete\Package\BasicTablePackage\Src\NewFieldTypes\Strategy\DropdownStrategy;

class DropdownField extends Field
{
    /**
     * @var DropdownStrategy
     */
    protected $validator;

    /**
     * @var DropdownStrategy
     */
    protected $editRepresentation;

    public function __construct($sqlFieldname, $label, $postName, $showInTable = true, $showInForm = true)
    {
        parent::__construct($sqlFieldname, $label, $postName, $showInTable, $showInForm);
        $strategy = new DropdownStrategy($this);
        $this->validator = $strategy;
        $this->editRepresentation = $strategy;
        $this->viewRepresentation = $strategy;
    }

    /**
     * Set the possible Options to choose from.
     * @param array $options array of the options like array('dbvalue' => 'displayvalue')
     * @return $this
     */
    public function setOptions($options)
    {
        $this->validator->setOptions($options);
        /**
         * @var DropdownStrategy $this ->editRepresentation;
         */
        $this->editRepresentation->setOptions($options);
        $this->viewRepresentation->setOptions($options);
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->validator->getOptions();
    }

}