<?php


namespace BasicTablePackage\Adapters\Concrete5;


use BasicTablePackage\Controller\VariableSetter;
use Concrete\Core\Block\BlockController;

class Concrete5VariableSetter implements VariableSetter
{
    /**
     * @var BlockController
     */
    private $blockController;

    /**
     * Concrete5VariableSetter constructor.
     * @param BlockController $blockController
     */
    public function __construct(BlockController $blockController)
    {
        $this->blockController = $blockController;
    }


    public function set(String $name, $value)
    {
        $this->blockController->set($name, $value);
    }
}