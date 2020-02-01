<?php

namespace BasicTablePackage\Test\Controller\ActionProcessors;


use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Controller\BasicTableController;
use BasicTablePackage\Entity\ExampleEntity;
use BasicTablePackage\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class DeleteEntryTest extends TestCase
{
    const TEST_1 = "test_value";
    /**
     * @var BasicTableController
     */
    private $basicTableController;

    public function test_delete_entry()
    {
        $this->basicTableController->getActionFor(ActionRegistryFactory::DELETE_ENTRY)
                                   ->process([], [], 1);

        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);
        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertStringNotContainsString(self::TEST_1, $output);
    }

    protected function setUp()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->createMock(EntityManager::class);
        /** @var Container $container */
        $this->basicTableController =
            DIContainerFactory::createContainer($entityManager, ExampleEntity::class)->get(BasicTableController::class);
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                   ->process([], ExampleEntityConstants::ENTRY_1_POST);
    }
}
