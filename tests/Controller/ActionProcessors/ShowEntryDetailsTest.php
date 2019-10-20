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

class ShowEntryDetailsTest extends TestCase
{
    /**
     * @var BasicTableController
     */
    private $basicTableController;

    public function test_show_details()
    {
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::SHOW_ENTRY_DETAILS)
            ->process([], [], 1);

        $output = ob_get_clean();
        $this->assertThat($output, Matchers::stringContainsKeysAndValues(ExampleEntityConstants::ENTRY_1_POST));
        $this->assertStringNotContainsString("<form", $output);
    }

    protected function setUp()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->createMock(EntityManager::class);
        /** @var Container $container */
        $this->basicTableController =
            DIContainerFactory::createContainer($entityManager, ExampleEntity::class)->get(BasicTableController::class);
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)->process([], ExampleEntityConstants::ENTRY_1_POST);
    }

}
