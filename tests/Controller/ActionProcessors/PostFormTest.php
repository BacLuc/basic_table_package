<?php

namespace BasicTablePackage\Test\Controller\ActionProcessors;


use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Controller\BasicTableController;
use BasicTablePackage\Entity\ExampleEntity;
use BasicTablePackage\Test\Constraints\Matchers;
use BasicTablePackage\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class PostFormTest extends TestCase
{
    /**
     * @var BasicTableController
     */
    private $basicTableController;

    public function test_post_form_new_entry()
    {
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                   ->process([], ExampleEntityConstants::ENTRY_1_POST);
        $this->basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertThat($output, Matchers::stringContainsKeysAndValues(ExampleEntityConstants::ENTRY_1_POST));
    }

    public function test_post_form_existing_entry()
    {
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                   ->process([], ExampleEntityConstants::ENTRY_1_POST);
        $changed_value = "changed_value";
        $changed_int_value = 203498;
        $changed_date_value = '2020-12-13';
        $changed_date_time_value = '2020-12-13 18:43';
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                   ->process([],
                                       [
                                           "value"          => $changed_value,
                                           "intcolumn"      => $changed_int_value,
                                           "datecolumn"     => $changed_date_value,
                                           "datetimecolumn" => $changed_date_time_value
                                       ], 1);

        $this->basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertStringNotContainsString(ExampleEntityConstants::TEXT_VAL_1, $output);
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains($changed_value));
        $this->assertThat($output, $this->stringContains("intcolumn"));
        $this->assertThat($output, $this->stringContains($changed_int_value));
        $this->assertThat($output, $this->stringContains("datecolumn"));
        $this->assertThat($output, $this->stringContains($changed_date_value));
        $this->assertThat($output, $this->stringContains("datetimecolumn"));
        $this->assertThat($output, $this->stringContains($changed_date_time_value));
        $this->assertThat($output, $this->stringContains("/1"));
    }

    protected function setUp()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->createMock(EntityManager::class);
        /** @var Container $container */
        $this->basicTableController =
            DIContainerFactory::createContainer($entityManager, ExampleEntity::class)->get(BasicTableController::class);
    }
}
