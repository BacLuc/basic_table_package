<?php

namespace BasicTablePackage\Test\Controller\ActionProcessors;


use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Controller\BasicTableController;
use BasicTablePackage\Entity\ExampleEntity;
use BasicTablePackage\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class ShowTableTest extends TestCase
{
    const TEST_1 = "test1";
    const TEST_2 = "test2";
    const TEST_3 = "test3";
    const TEST_4 = "test4";


    private $basicTableController;

    public function test_sets_headers_and_rows_to_TableView_retrieved_from_TableViewService()
    {
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);;

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains("intcolumn"));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains(self::TEST_2));
        $this->assertThat($output, $this->stringContains(self::TEST_3));
        $this->assertThat($output, $this->stringContains(self::TEST_4));
    }

    protected function setUp()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->createMock(EntityManager::class);

        /** @var Container $container */
        $this->basicTableController =
            DIContainerFactory::createContainer($entityManager, ExampleEntity::class)->get(BasicTableController::class);
        collect([self::TEST_1, self::TEST_2, self::TEST_3, self::TEST_4])->each(function (string $value) {
            $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                       ->process([], ["value" => $value]);
        });
    }
}
