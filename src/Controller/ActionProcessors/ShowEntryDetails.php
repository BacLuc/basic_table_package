<?php


namespace BaclucC5Crud\Controller\ActionProcessors;


use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\PaginationConfiguration;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\TableViewService;
use BaclucC5Crud\View\TableView\Row;
use function BaclucC5Crud\Lib\collect as collect;

class ShowEntryDetails implements ActionProcessor
{
    const DETAIL_VIEW = "view/detail";
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
     * ShowFormActionProcessor constructor.
     * @param TableViewService $tableViewService
     * @param VariableSetter $variableSetter
     * @param Renderer $renderer
     */
    public function __construct(TableViewService $tableViewService, VariableSetter $variableSetter, Renderer $renderer)
    {
        $this->tableViewService = $tableViewService;
        $this->variableSetter = $variableSetter;
        $this->renderer = $renderer;
    }


    function getName(): string
    {
        return ActionRegistryFactory::SHOW_ENTRY_DETAILS;
    }

    function process(array $get, array $post, ...$additionalParameters)
    {
        $editId = null;
        if (count($additionalParameters) == 1) {
            $editId = intval($additionalParameters[0]);
        }

        $tableView = $this->tableViewService->getTableView(new PaginationConfiguration(0, null));
        /**
         * @var Row $detailEntry
         */
        $detailEntry = collect($tableView->getRows())->first(function (Row $row) use ($editId) {
            return $row->getId() == $editId;
        });
        $headersAndValues = collect($tableView->getHeaders())->combine($detailEntry);
        $this->variableSetter->set("properties", $headersAndValues);
        $this->renderer->render(self::DETAIL_VIEW);
    }

}