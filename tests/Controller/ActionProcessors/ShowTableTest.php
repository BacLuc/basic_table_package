<?php

namespace BaclucC5Crud\Test\Controller\ActionProcessors;


use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Entity\ExampleEntity;
use BaclucC5Crud\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class ShowTableTest extends TestCase
{
    const TEST_1 = "test1";
    const TEST_2 = "test2";
    const TEST_3 = "test3";
    const TEST_4 = "test4";


    private $crudController;

    public function test_sets_headers_and_rows_to_TableView_retrieved_from_TableViewService()
    {
        ob_start();
        $this->crudController->getActionFor(ActionRegistryFactory::SHOW_TABLE, "1", "1")->process([], []);;

        $output = ob_get_clean();
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains("intcolumn"));
        $this->assertThat($output, $this->stringContains(self::TEST_1));
        $this->assertThat($output, $this->stringContains(self::TEST_2));
        $this->assertThat($output, $this->stringContains(self::TEST_3));
        $this->assertThat($output, $this->stringContains(self::TEST_4));
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
        $this->crudController =
            $container->get(CrudController::class);
        collect([self::TEST_1, self::TEST_2, self::TEST_3, self::TEST_4])->each(function (string $value) {
            $postValues = ExampleEntityConstants::ENTRY_1_POST;
            $postValues["value"] = $value;
            $this->crudController->getActionFor(ActionRegistryFactory::POST_FORM, "1", "1")
                                 ->process([], $postValues);
        });
    }
}
