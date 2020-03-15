<?php

namespace BaclucC5Crud\Test\Controller\ActionProcessors;


use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Entity\ExampleEntity;
use BaclucC5Crud\Test\Constraints\Matchers;
use BaclucC5Crud\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class ShowEditEntryFormTest extends TestCase
{
    /**
     * @var CrudController
     */
    private $crudController;

    public function test_edit_form_shows_value_of_existing_entry()
    {
        ob_start();
        $this->crudController->getActionFor(ActionRegistryFactory::EDIT_ROW_FORM, "0")
                             ->process([], [], 1);

        $output = ob_get_clean();
        $this->assertThat($output,
            Matchers::stringContainsKeysAndValuesRecursive(ExampleEntityConstants::ENTRY_1_POST));
        $this->assertThat($output, $this->stringContains("action=\"post_form/1\""));
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function setUp()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->createMock(EntityManager::class);
        /** @var Container $container */
        $container = DIContainerFactory::createContainer($entityManager, ExampleEntity::class);
        ExampleEntityConstants::addReferencedEntityTestValues($container);
        $this->crudController = $container->get(CrudController::class);
        $this->crudController->getActionFor(ActionRegistryFactory::POST_FORM, "0")
                             ->process([], ExampleEntityConstants::ENTRY_1_POST);
    }
}
