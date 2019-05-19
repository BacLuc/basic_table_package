<?php


namespace BasicTablePackage\Controller;


class BasicTableController
{
    const TABLE_VIEW = "View/table";
    const FORM_VIEW  = "View/form";
    /**
     * @var Renderer
     */
    private $renderer;

    public function __construct ($obj = null, Renderer $renderer)
    {
        $this->renderer = $renderer;
        $renderer->render(self::TABLE_VIEW);
    }

    public function openForm (int $editId = null)
    {
        $this->renderer->render(self::FORM_VIEW);
    }


}