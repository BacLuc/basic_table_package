<?php


namespace BasicTablePackage\Adapters\Concrete5;


use BadMethodCallException;
use BasicTablePackage\Controller\Renderer;
use Concrete\Core\Block\BlockController;

class Concrete5Renderer implements Renderer
{
    /**
     * @var BlockController
     */
    private $blockController;

    /**
     * Concrete5Renderer constructor.
     * @param BlockController $blockController
     */
    public function __construct (BlockController $blockController) { $this->blockController = $blockController; }


    public function render (string $path)
    {
        $this->blockController->render("../../resources/" . $path);
    }

    public function action (string $action)
    {
        throw new BadMethodCallException("this method is not implemented");
    }
}