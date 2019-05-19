<?php


namespace BasicTablePackage\Controller;


use BasicTablePackage\Controller\Renderer;

class BasicTableController
{
    const TABLE_VIEW = "View/table";
    /**
     * @var Renderer
     */
    private $renderer;

    public function __construct ($obj = null, Renderer $renderer) {
        $this->renderer = $renderer;
        $renderer->render(self::TABLE_VIEW);
    }
}