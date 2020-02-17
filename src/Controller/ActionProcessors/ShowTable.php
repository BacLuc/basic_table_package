<?php


namespace BaclucC5Crud\Controller\ActionProcessors;


use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\TableViewService;
use BaclucC5Crud\View\ViewActionRegistry;

class ShowTable implements ActionProcessor
{
    const TABLE_VIEW = "view/table";
    /**
     * @var TableViewService
     */
    private $tableViewService;
    /**
     * @var VariableSetter
     */
    private $variableSetter;
    /**
     * @var Renderer
     */
    private $renderer;
    /**
     * @var ViewActionRegistry
     */
    private $viewActionRegistry;

    public function __construct(
        TableViewService $tableViewService,
        VariableSetter $variableSetter,
        Renderer $renderer,
        ViewActionRegistry $viewActionRegistry
    ) {
        $this->tableViewService = $tableViewService;
        $this->variableSetter = $variableSetter;
        $this->renderer = $renderer;
        $this->viewActionRegistry = $viewActionRegistry;
    }

    function getName(): string
    {
        return ActionRegistryFactory::SHOW_TABLE;
    }


    function process(array $get, array $post, ...$additionalParameters)
    {
        $tableView = $this->tableViewService->getTableView();
        $this->variableSetter->set("headers", $tableView->getHeaders());
        $this->variableSetter->set("rows", $tableView->getRows());
        $this->variableSetter->set("actions",
            [$this->viewActionRegistry->getByName(ActionRegistryFactory::ADD_NEW_ROW_FORM)]);
        $this->variableSetter->set("rowactions",
            [
                $this->viewActionRegistry->getByName(ActionRegistryFactory::EDIT_ROW_FORM),
                $this->viewActionRegistry->getByName(ActionRegistryFactory::DELETE_ENTRY),
                $this->viewActionRegistry->getByName(ActionRegistryFactory::SHOW_ENTRY_DETAILS),
            ]);
        $this->renderer->render(self::TABLE_VIEW);
    }

}