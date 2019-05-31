<?php
/** @noinspection PhpParamsInspection */

namespace BasicTablePackage\Controller;

use BasicTablePackage\TableViewService;
use BasicTablePackage\Test\DIContainerFactory;
use BasicTablePackage\View\TableView\Row;
use BasicTablePackage\View\TableView\TableView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function DI\factory;

class BasicTableControllerTest extends TestCase
{
    const TEST_1   = "test1";
    const TEST_2   = "test2";
    const TEST_3   = "test3";
    const TEST_4   = "test4";
    const HEADER_1 = "header1";
    const HEADER_2 = "header2";


    /**
     * @var MockObject
     */
    private $renderer;
    /**
     * @var MockObject
     */

    private $tableViewService;
    private $variableSetter;
    /**
     * @var BasicTableController
     */
    private $basicTableController;


    protected function setUp ()
    {
        $this->renderer = $this->createMock(Renderer::class);
        $this->tableViewService = $this->createMock(TableViewService::class);
        $this->variableSetter = $this->createMock(VariableSetter::class);

        $this->basicTableController =
            new BasicTableController($this->renderer, $this->tableViewService, $this->variableSetter, null);
    }


    public function test_renders_table_view_when_view_called ()
    {
        $this->renderer->expects($this->once())->method('render')->with(BasicTableController::TABLE_VIEW);

        $this->basicTableController->view();
    }

    public function test_renders_form_view_when_edit_action_called ()
    {
        $this->renderer->expects($this->once())->method('render')->with(BasicTableController::FORM_VIEW);

        $this->basicTableController->openForm(null);
    }

    public function test_sets_headers_and_rows_to_TableView_retrieved_from_TableViewService ()
    {
        $row1 = new Row([ self::TEST_1, self::TEST_2 ]);
        $row2 = new Row([ self::TEST_3, self::TEST_4 ]);
        $tableView = new TableView([ self::HEADER_1, self::HEADER_2 ], [ $row1, $row2 ]);


        /** @var Cont $container */
        $container = DIContainerFactory::createContainer();
        $container->set(TableViewService::class, factory(function () use (
            $tableView
        ) {
            return new MockTableViewService($tableView);
        }));


        ob_start();
        /**
         * @var $basicTableController BasicTableController
         */
        $basicTableController = $container->get(BasicTableController::class);
        $basicTableController->view();
        $output = ob_get_clean();

        $this->assertThat($output, $this->stringContains(self::HEADER_1));
        $this->assertThat($output, $this->stringContains(self::HEADER_2));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains(self::TEST_2));
        $this->assertThat($output, $this->stringContains(self::TEST_3));
        $this->assertThat($output, $this->stringContains(self::TEST_4));
    }
}

class MockTableViewService extends TableViewService
{
    /**
     * @var TableView
     */
    private $tableView;

    /**
     * MockTableViewService constructor.
     * @param TableView $tableView
     */
    public function __construct (TableView $tableView)
    {
        $this->tableView = $tableView;
    }

    public function getTableView (): TableView
    {
        return $this->tableView;
    }
}
