<?php
/** @noinspection PhpParamsInspection */

namespace BasicTablePackage\Controller;

use BasicTablePackage\FormViewService;
use BasicTablePackage\TableViewService;
use BasicTablePackage\Test\DIContainerFactory;
use BasicTablePackage\View\FormView\FormView;
use BasicTablePackage\View\FormView\TextField;
use BasicTablePackage\View\TableView\Row;
use BasicTablePackage\View\TableView\TableView;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use function DI\value;

class BasicTableControllerTest extends TestCase
{
    const TEST_1   = "test1";
    const TEST_2   = "test2";
    const TEST_3   = "test3";
    const TEST_4   = "test4";
    const HEADER_1 = "header1";
    const HEADER_2 = "header2";


    private $tableViewService;
    private $container;
    private $formViewService;
    private $entityManager;


    protected function setUp ()
    {
        $this->tableViewService = $this->createMock(TableViewService::class);
        $this->formViewService = $this->createMock(FormViewService::class);
        $this->entityManager = $this->createMock(EntityManager::class);

        /** @var Container $container */
        $this->container = DIContainerFactory::createContainer($this->entityManager);
    }

    /**
     * @dataProvider createTableActionNames
     */
    public function test_sets_headers_and_rows_to_TableView_retrieved_from_TableViewService ($action)
    {
        $row1 = new Row([ self::TEST_1, self::TEST_2 ]);
        $row2 = new Row([ self::TEST_3, self::TEST_4 ]);
        $tableView = new TableView([ self::HEADER_1, self::HEADER_2 ], [ $row1, $row2 ]);

        $this->tableViewService->expects($this->once())->method('getTableView')->willReturn($tableView);

        $this->container->set(TableViewService::class, value($this->tableViewService));


        ob_start();
        /**
         * @var $basicTableController BasicTableController
         */
        $basicTableController = $this->container->get(BasicTableController::class);
        $basicTableController->getActionFor($action)->process([], []);;

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains(self::HEADER_1));
        $this->assertThat($output, $this->stringContains(self::HEADER_2));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains(self::TEST_2));
        $this->assertThat($output, $this->stringContains(self::TEST_3));
        $this->assertThat($output, $this->stringContains(self::TEST_4));
    }

    public function createTableActionNames ()
    {
        return [
            [ ActionRegistryFactory::SHOW_TABLE ],
            [ ActionRegistryFactory::POST_FORM ],
        ];
    }

    public function test_show_form_view ()
    {
        $formView =
            new FormView([ new TextField(self::HEADER_1, self::TEST_1), new TextField(self::HEADER_2, self::TEST_2) ]);

        $this->formViewService->expects($this->once())->method('getFormView')->willReturn($formView);

        $this->container->set(FormViewService::class, value($this->formViewService));


        ob_start();
        /**
         * @var $basicTableController BasicTableController
         */
        $basicTableController = $this->container->get(BasicTableController::class);
        $basicTableController->getActionFor(ActionRegistryFactory::ADD_NEW_ROW_FORM)->process([], []);

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains(self::HEADER_1));
        $this->assertThat($output, $this->stringContains(self::HEADER_2));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains(self::TEST_2));
    }

}

