<?php

namespace BasicTablePackage\Test\Controller\ActionProcessors;


use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Controller\BasicTableController;
use BasicTablePackage\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class ShowNewEntryFormTest extends TestCase
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
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)->process([], ["value" => self::TEST_1]);
    }

    public function test_new_row_form_has_empty_fields()
    {
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::ADD_NEW_ROW_FORM)
            ->process([], []);

        $output = ob_get_clean();
        $this->assertStringNotContainsString(self::TEST_1, $output);
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains("action=\"post_form\""));
    }
}
