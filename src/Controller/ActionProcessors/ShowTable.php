<?php


namespace BaclucC5Crud\Controller\ActionProcessors;


use BaclucC5Crud\Controller\ActionConfiguration;
use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\PaginationParser;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\RowActionConfiguration;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\TableViewService;
use BaclucC5Crud\View\FormView\IntegerField;

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
     * @var ActionConfiguration
     */
    private $actionConfiguration;
    /**
     * @var RowActionConfiguration
     */
    private $rowActionConfiguration;
    /**
     * @var PaginationParser
     */
    private $paginationParser;

    public function __construct(
        TableViewService $tableViewService,
        VariableSetter $variableSetter,
        Renderer $renderer,
        ActionConfiguration $actionConfiguration,
        RowActionConfiguration $rowActionConfiguration,
        PaginationParser $paginationParser
    ) {
        $this->tableViewService = $tableViewService;
        $this->variableSetter = $variableSetter;
        $this->renderer = $renderer;
        $this->actionConfiguration = $actionConfiguration;
        $this->rowActionConfiguration = $rowActionConfiguration;
        $this->paginationParser = $paginationParser;
    }

    function getName(): string
    {
        return ActionRegistryFactory::SHOW_TABLE;
    }


    function process(array $get, array $post, ...$additionalParameters)
    {
        $paginationConfiguration = $this->paginationParser->parse($get);
        $tableView = $this->tableViewService->getTableView($paginationConfiguration);
        $this->variableSetter->set("headers", $tableView->getHeaders());
        $this->variableSetter->set("rows", $tableView->getRows());
        $this->variableSetter->set("actions", $this->actionConfiguration->getActions());
        $this->variableSetter->set("rowactions", $this->rowActionConfiguration->getActions());
        $this->variableSetter->set("count", $tableView->getCount());
        $this->variableSetter->set("currentPage", $paginationConfiguration->getCurrentPage());
        $this->variableSetter->set("pageSize", $paginationConfiguration->getPageSize());
        $pageSizeField = new IntegerField("Entries to display", "pageSize", $paginationConfiguration->getPageSize());
        $this->variableSetter->set("pageSizeField", $pageSizeField);
        $this->renderer->render(self::TABLE_VIEW);
    }

}