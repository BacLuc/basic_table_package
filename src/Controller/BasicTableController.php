<?php


namespace BasicTablePackage\Controller;


use BasicTablePackage\TableViewService;
use BasicTablePackage\View\Action;

class BasicTableController
{
    const TABLE_VIEW = "view/table";
    const FORM_VIEW  = "view/form";
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

    public function __construct (Renderer $renderer, TableViewService $tableViewService, VariableSetter $variableSetter,
                                 $obj = null)
    {
        $this->renderer = $renderer;
        $this->tableViewService = $tableViewService;
        $this->variableSetter = $variableSetter;
    }

    public function view(){
        $tableView = $this->tableViewService->getTableView();
        $this->variableSetter->set("headers", $tableView->getHeaders());
        $this->variableSetter->set("rows", $tableView->getRows());
        $actions = [];
        $actions[] = new Action("add_new_row_form", "add", "new Entry", "new Entry", "fa-plus");
        $this->variableSetter->set("actions", $actions);
        $this->renderer->render(self::TABLE_VIEW);
    }

    public function openForm (int $editId = null)
    {
        $this->renderer->render(self::FORM_VIEW);
    }


}