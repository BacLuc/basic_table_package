<?php

namespace BaclucC5Crud\Test\Controller\ActionProcessors;


use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Entity\ExampleEntity;
use BaclucC5Crud\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class DeleteEntryTest extends TestCase
{
    const TEST_1 = "test_value";
    /**
     * @var CrudController
     */
    private $crudController;

    public function test_delete_entry()
    {
        $this->crudController->getActionFor(ActionRegistryFactory::DELETE_ENTRY)
                             ->process([], [], 1);

        ob_start();
        $this->crudController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);
        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertStringNotContainsString(self::TEST_1, $output);
    }

    protected function setUp()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->createMock(EntityManager::class);
        /** @var Container $container */
        $this->crudController =
            DIContainerFactory::createContainer($entityManager, ExampleEntity::class)->get(CrudController::class);
        $this->crudController->getActionFor(ActionRegistryFactory::POST_FORM)
                             ->process([], ExampleEntityConstants::ENTRY_1_POST);
    }
}
