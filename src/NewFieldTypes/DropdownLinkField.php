<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 02.05.17
 * Time: 11:25
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes;


use Concrete\Package\BasicTablePackage\Src\NewFieldTypes\Strategy\DropDownLinkStrategy;

class DropdownLinkField extends AbstractAssociationField
{

    public function __construct($sqlFieldname, $label, $postName, $showInTable = true, $showInForm = true)
    {
        parent::__construct($sqlFieldname, $label, $postName, $showInTable, $showInForm);
        $strategy = new DropDownLinkStrategy($this);
        $this->validator = $strategy;
        $this->editRepresentation = $strategy;
        $this->viewRepresentation = $strategy;
    }


}