<?php

namespace BasicTablePackage\Test\Controller\ActionProcessors;


use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Controller\BasicTableController;
use BasicTablePackage\Entity\ExampleEntity;
use BasicTablePackage\Entity\ExampleEntityDropdownValueSupplier;
use BasicTablePackage\Test\Constraints\Matchers;
use BasicTablePackage\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class PostFormTest extends TestCase
{
    /**
     * @var BasicTableController
     */
    private $basicTableController;

    public function test_post_form_new_entry()
    {
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                   ->process([], ExampleEntityConstants::ENTRY_1_POST);
        $this->basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertThat($output, Matchers::stringContainsKeysAndValues(ExampleEntityConstants::getValues()));
    }

    public function test_post_form_existing_entry()
    {
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                   ->process([], ExampleEntityConstants::ENTRY_1_POST);
        $secondPostArray = [
            "value"          => "changed_value",
            "intcolumn"      => 203498,
            "datecolumn"     => '2020-12-13',
            "datetimecolumn" => '2020-12-13 18:43',
            "wysiwygcolumn"  => BigTestValues::WYSIWYGVALUE2,
            "dropdowncolumn" => ExampleEntityDropdownValueSupplier::KEY_6,
            "manyToOne"      => ExampleEntityConstants::REFERENCED_ENTITY_ID_2,
            "manyToMany"     => [ExampleEntityConstants::REFERENCED_ENTITY_ID_1],
        ];
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                   ->process([],
                                       $secondPostArray,
                                       1);

        $this->basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertStringNotContainsString(ExampleEntityConstants::TEXT_VAL_1, $output);

        $tableValuesArray = $secondPostArray;
        $exampleEntityDropdownValueSupplier = new ExampleEntityDropdownValueSupplier();
        $tableValuesArray["dropdowncolumn"] =
            $exampleEntityDropdownValueSupplier->getValues()[$tableValuesArray["dropdowncolumn"]];

        $referencedEntityValues = ExampleEntityConstants::getReferencedEntityValues();
        $tableValuesArray["manyToOne"] = $referencedEntityValues[$tableValuesArray["manyToOne"]];
        $tableValuesArray["manyToMany"] = $referencedEntityValues[$tableValuesArray["manyToMany"][0]];

        $this->assertThat($output, Matchers::stringContainsKeysAndValuesRecursive($tableValuesArray));


        $this->assertThat($output, $this->stringContains("/1"));
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
        $this->basicTableController = $container->get(BasicTableController::class);
    }
}
