<?php

namespace BasicTablePackage\Test\Controller\ActionProcessors;


use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Controller\BasicTableController;
use BasicTablePackage\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class PostFormTest extends TestCase
{
    const TEST_1 = "test_value";
    /**
     * @var BasicTableController
     */
    private $basicTableController;

    protected function setUp()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->createMock(EntityManager::class);
        /** @var Container $container */
        $this->basicTableController = DIContainerFactory::createContainer($entityManager)->get(BasicTableController::class);
    }

    public function test_post_form_new_entry()
    {
        ob_start();
        $intcolvalue = 549864;
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)->process([], ["value" => self::TEST_1, "intcolumn" => $intcolvalue]);
        $this->basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains("intcolumn"));
        $this->assertThat($output, $this->stringContains($intcolvalue));
    }

    public function test_post_form_existing_entry()
    {
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)->process([], ["value" => self::TEST_1, "intcolumn" => 65498]);
        $changed_value = "changed_value";
        $changed_int_value = 203498;
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
            ->process([], ["value" => $changed_value, "intcolumn" => $changed_int_value], 1);

        $this->basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertStringNotContainsString(self::TEST_1, $output);
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains($changed_value));
        $this->assertThat($output, $this->stringContains("intcolumn"));
        $this->assertThat($output, $this->stringContains($changed_int_value));
        $this->assertThat($output, $this->stringContains("/1"));
    }
}
