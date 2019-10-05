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

    public function test_sets_headers_and_rows_to_TableView_retrieved_from_TableViewService ()
    {
        $row1 = new Row(1, [ self::TEST_1, self::TEST_2 ]);
        $row2 = new Row(2, [ self::TEST_3, self::TEST_4 ]);
        $tableView = new TableView([ self::HEADER_1, self::HEADER_2 ], [ $row1, $row2 ]);

        $this->tableViewService->expects($this->once())->method('getTableView')->willReturn($tableView);

        $this->container->set(TableViewService::class, value($this->tableViewService));


        ob_start();
        /**
         * @var $basicTableController BasicTableController
         */
        $basicTableController = $this->container->get(BasicTableController::class);
        $basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);;

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains(self::HEADER_1));
        $this->assertThat($output, $this->stringContains(self::HEADER_2));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains(self::TEST_2));
        $this->assertThat($output, $this->stringContains(self::TEST_3));
        $this->assertThat($output, $this->stringContains(self::TEST_4));
    }

    public function test_show_form_view ()
    {
        $formView =
            new FormView([ new TextField(self::HEADER_1, self::HEADER_1, self::TEST_1),
                           new TextField(self::HEADER_2, self::HEADER_1, self::TEST_2),
                         ]);

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

    public function test_when_form_is_submitted_then_new_entry_is_available ()
    {
        /**
         * @var BasicTableController $basicTableController
         */
        $basicTableController = $this->container->get(BasicTableController::class);

        ob_start();
        $basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertStringNotContainsString(self::TEST_1, $output);

        ob_start();
        $basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)->process([], [ "value" => self::TEST_1 ]);
        $basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains(self::TEST_1));

    }

    public function test_edit_form_shows_value_of_existing_entry ()
    {
        /**
         * @var BasicTableController $basicTableController
         */
        $basicTableController = $this->container->get(BasicTableController::class);

        ob_start();
        $basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)->process([], [ "value" => self::TEST_1 ]);
        $basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains("/1"));

        ob_start();
        $basicTableController->getActionFor(ActionRegistryFactory::EDIT_ROW_FORM)
                             ->process([], [], 1);

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains("action=\"post_form/1\""));
    }


    public function test_editing_an_entry_changes_entry_in_repositry ()
    {
        /**
         * @var BasicTableController $basicTableController
         */
        $basicTableController = $this->container->get(BasicTableController::class);

        ob_start();
        $basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)->process([], [ "value" => self::TEST_1 ]);
        $basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains("/1"));

        ob_start();
        $changed_value = "changed_value";
        $basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                             ->process([], [ "value" => $changed_value ], 1);

        $basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertStringNotContainsString(self::TEST_1, $output);
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains($changed_value));
        $this->assertThat($output, $this->stringContains("/1"));
    }

    public function test_delete_entry ()
    {
        /**
         * @var BasicTableController $basicTableController
         */
        $basicTableController = $this->container->get(BasicTableController::class);

        ob_start();
        $basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)->process([], [ "value" => self::TEST_1 ]);
        $basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains("/1"));

        ob_start();
        $basicTableController->getActionFor(ActionRegistryFactory::DELETE_ENTRY)
                             ->process([], [], 1);

        $basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);
        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertStringNotContainsString(self::TEST_1, $output);
    }

}

