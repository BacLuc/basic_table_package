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

class ShowNewEntryFormTest extends TestCase
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
        $this->basicTableController =
            DIContainerFactory::createContainer($entityManager, ExampleEntity::class)->get(BasicTableController::class);
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)->process([], ExampleEntityConstants::ENTRY_1_POST);
    }

    public function test_new_row_form_has_empty_fields()
    {
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::ADD_NEW_ROW_FORM)
            ->process([], []);

        $output = ob_get_clean();
        $this->assertStringNotContainsString(ExampleEntityConstants::TEXT_VAL_1, $output);
        $this->assertStringNotContainsString(ExampleEntityConstants::INT_VAL_1, $output);
        $this->assertStringNotContainsString(ExampleEntityConstants::DATE_VALUE_1, $output);
        $this->assertStringNotContainsString(ExampleEntityConstants::DATETIME_VALUE_1, $output);
        $this->assertThat($output, Matchers::stringContainsAll(array_keys(ExampleEntityConstants::ENTRY_1_POST)));
        $this->assertThat($output, $this->stringContains("action=\"post_form\""));
    }
}
