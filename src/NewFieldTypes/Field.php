<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.04.17
 * Time: 17:47
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes;


use Concrete\Package\BasicTablePackage\Src\NewFieldTypes\Strategy\TextStrategy;

class Field extends AbstractField
{
    public function __construct($sqlFieldname, $label, $postName, $showInTable = true, $showInForm = true)
    {
        parent::__construct($sqlFieldname, $label, $postName, $showInTable, $showInForm);
        $strategy = new TextStrategy($this);
        $this->validator = $strategy;
        $this->viewRepresentation = $strategy;
        $this->editRepresentation = $strategy;
    }

    public function getView()
    {
        // TODO: Implement getView() method.
    }

    public function setView($view)
    {
        // TODO: Implement setView() method.
    }


}