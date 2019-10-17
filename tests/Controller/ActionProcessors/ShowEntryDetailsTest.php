<?php

namespace BasicTablePackage\Test\Controller\ActionProcessors;


use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Controller\BasicTableController;
use BasicTablePackage\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class ShowEntryDetailsTest extends TestCase
{
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
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)->process([], ExampleEntityConstants::ENTRY_1_POST);
    }

    public function test_show_details()
    {
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::SHOW_ENTRY_DETAILS)
            ->process([], [], 1);

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains(ExampleEntityConstants::TEXT_VAL_1));
        $this->assertThat($output, $this->stringContains("intcolumn"));
        $this->assertThat($output, $this->stringContains(ExampleEntityConstants::INT_VAL_1));
        $this->assertThat($output, $this->stringContains("datecolumn"));
        $this->assertThat($output, $this->stringContains(ExampleEntityConstants::DATE_VALUE_1));
        $this->assertStringNotContainsString("<form", $output);
    }

}
