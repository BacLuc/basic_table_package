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
    const TEST_1 = "test_value";
    const INT_VAL = 42;
    const DATE_VALUE = '2020-12-12';
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
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)->process([], ["value" => self::TEST_1, "intcolumn" => self::INT_VAL, "datecolumn" => self::DATE_VALUE]);
    }

    public function test_show_details()
    {
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::SHOW_ENTRY_DETAILS)
            ->process([], [], 1);

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains("intcolumn"));
        $this->assertThat($output, $this->stringContains(self::INT_VAL));
        $this->assertThat($output, $this->stringContains("datecolumn"));
        $this->assertThat($output, $this->stringContains(self::DATE_VALUE));
        $this->assertStringNotContainsString("<form", $output);
    }

}
