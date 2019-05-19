<?php


namespace BasicTablePackage\Adapters\Concrete5;


use Concrete\Core\Block\BlockController;
use BasicTablePackage\Controller\Renderer;

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
        $this->blockController->render("../../src/".$path);
    }
}