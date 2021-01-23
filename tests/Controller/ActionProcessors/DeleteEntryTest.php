<?php

namespace BaclucC5Crud\Test\Controller\ActionProcessors;

use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Entity\ExampleEntity;
use BaclucC5Crud\Test\DIContainerFactory;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class DeleteEntryTest extends TestCase {
    const TEST_1 = 'test_value';
    /**
     * @var CrudController
     */
    private $crudController;

    protected function setUp() {
        /** @var EntityManager $entityManager */
        $entityManager = $this->createMock(EntityManager::class);
        $container = DIContainerFactory::createContainer($entityManager, ExampleEntity::class);
        $this->crudController =
            $container->get(CrudController::class);
        ExampleEntityConstants::addReferencedEntityTestValues($container);
        $this->crudController->getActionFor(ActionRegistryFactory::POST_FORM, '0')
            ->process([], ExampleEntityConstants::ENTRY_1_POST)
        ;
    }

    public function testDeleteEntry() {
        $this->crudController->getActionFor(ActionRegistryFactory::DELETE_ENTRY, '0')
            ->process([], [], 1)
        ;

        ob_start();
        $this->crudController->getActionFor(ActionRegistryFactory::SHOW_TABLE, '0')->process([], []);
        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains('value'));
        $this->assertStringNotContainsString(self::TEST_1, $output);
    }
}
