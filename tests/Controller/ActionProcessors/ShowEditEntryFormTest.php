<?php

namespace BasicTablePackage\Test\Controller\ActionProcessors;


use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Controller\BasicTableController;
use BasicTablePackage\Test\Constraints\Matchers;
use BasicTablePackage\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class ShowEditEntryFormTest extends TestCase
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

    public function test_edit_form_shows_value_of_existing_entry()
    {
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::EDIT_ROW_FORM)
            ->process([], [], 1);

        $output = ob_get_clean();
        $this->assertThat($output, Matchers::stringContainsKeysAndValues(ExampleEntityConstants::ENTRY_1_POST));
        $this->assertThat($output, $this->stringContains("action=\"post_form/1\""));
    }
}
