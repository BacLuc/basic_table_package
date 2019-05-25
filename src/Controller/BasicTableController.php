<?php


namespace BasicTablePackage\Controller;


use BasicTablePackage\TableViewService;

class BasicTableController
{
    const TABLE_VIEW = "View/table";
    const FORM_VIEW  = "View/form";
    /**
     * @var Renderer
     */
    private $renderer;
    /**
     * @var TableViewService
     */
    private $tableViewService;
    /**
     * @var VariableSetter
     */
    private $variableSetter;

    public function __construct ($obj = null, Renderer $renderer, TableViewService $tableViewService, VariableSetter $variableSetter)
    {
        $this->renderer = $renderer;
        $this->tableViewService = $tableViewService;
        $this->variableSetter = $variableSetter;

        $tableView = $this->tableViewService->getTableView();
        $variableSetter->set("headers", $tableView->getHeaders());
        $variableSetter->set("rows", $tableView->getRows());
        $renderer->render(self::TABLE_VIEW);
    }

    public function openForm (int $editId = null)
    {
        $this->renderer->render(self::FORM_VIEW);
    }


}