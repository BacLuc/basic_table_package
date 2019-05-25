<?php
/** @noinspection PhpParamsInspection */

namespace BasicTablePackage\Controller;

use BasicTablePackage\Adapters\DefaultContext;
use BasicTablePackage\Adapters\DefaultRenderer;
use BasicTablePackage\TableViewService;
use BasicTablePackage\View\TableView\Row;
use BasicTablePackage\View\TableView\TableView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
    private $defaultContext;
    private $defaultRenderer;
    private $variableSetter;

    protected function setUp ()
    {
        $this->renderer = $this->createMock(Renderer::class);
        $this->tableViewService = $this->createMock(TableViewService::class);
        $this->variableSetter = $this->createMock(VariableSetter::class);

        $this->defaultContext = new DefaultContext();
        $this->defaultRenderer = new DefaultRenderer($this->defaultContext);
    }


    public function test_renders_table_view_when_created ()
    {
        $this->renderer->expects($this->once())->method('render')->with(BasicTableController::TABLE_VIEW);

        $this->createController();
    }

    public function test_renders_form_view_when_edit_action_called ()
    {
        $this->renderer->expects($this->exactly(2))->method('render')
                       ->withConsecutive([ $this->equalTo(BasicTableController::TABLE_VIEW) ],
                                         [ $this->equalTo(BasicTableController::FORM_VIEW) ])
        ;

        $basicTableController = $this->createController();

        $basicTableController->openForm(null);
    }

    public function test_sets_headers_and_rows_to_TableView_retrieved_from_TableViewService() {
        $row1 = new Row([ self::TEST_1, self::TEST_2 ]);
        $row2 = new Row([ self::TEST_3, self::TEST_4 ]);
        $tableView = new TableView([ self::HEADER_1, self::HEADER_2 ], [ $row1, $row2]);

        $this->tableViewService->expects($this->once())->method("getTableView")->willReturn($tableView);

        ob_start();
        $this->createController($this->defaultRenderer, $this->defaultContext);
        $output = ob_get_clean();

        $this->assertThat($output, $this->stringContains(self::HEADER_1));
        $this->assertThat($output, $this->stringContains(self::HEADER_2));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains(self::TEST_2));
        $this->assertThat($output, $this->stringContains(self::TEST_3));
        $this->assertThat($output, $this->stringContains(self::TEST_4));
    }

    /**
     * @return BasicTableController
     */
    protected function createController (Renderer $renderer = null, VariableSetter $variableSetter = null): BasicTableController
    {
        $renderer = $renderer ?: $this->renderer;
        $variableSetter = $variableSetter ?: $this->variableSetter;
        $basicTableController = new BasicTableController(null, $renderer, $this->tableViewService, $variableSetter);
        return $basicTableController;
    }

}
