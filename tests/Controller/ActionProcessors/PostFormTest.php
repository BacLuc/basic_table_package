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

        ob_start();
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

    public function test_post_form_validation_new_entry()
    {
        $ENTRY_1_POST = ExampleEntityConstants::ENTRY_1_POST;
        $ENTRY_1_POST['intcolumn'] = "invalidInt";
        $ENTRY_1_POST['datecolumn'] = "invalidDate";
        $ENTRY_1_POST['datetimecolumn'] = "invalidDateTime";
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                   ->process([], $ENTRY_1_POST);

        $output = ob_get_clean();

        $form_fields = $ENTRY_1_POST;
        $form_fields['dropdowncolumn'] = "<option value=\"" . ExampleEntityConstants::DROPDOWN_KEY_5 . "\" selected>";
        $form_fields['manyToOne'] = "<option value=\"1\" selected>";
        $form_fields['manyToMany'] = "<option value=\"1\" selected>";
        $this->assertThat($output, Matchers::stringContainsKeysAndValues($form_fields));
        //second selected entry of manyToOne
        $this->assertThat($output, $this->stringContains("<option value=\"2\" selected>"));
    }

    public function test_post_form_validation_notnull()
    {
        $ENTRY_1_POST = ExampleEntityConstants::ENTRY_1_POST;
        unset($ENTRY_1_POST['wysiwygcolumn']);
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                   ->process([], $ENTRY_1_POST);

        $output = ob_get_clean();

        $form_fields = $ENTRY_1_POST;
        $form_fields['dropdowncolumn'] = "<option value=\"" . ExampleEntityConstants::DROPDOWN_KEY_5 . "\" selected>";
        $form_fields['manyToOne'] = "<option value=\"1\" selected>";
        $form_fields['manyToMany'] = "<option value=\"1\" selected>";
        $this->assertThat($output, Matchers::stringContainsKeysAndValues($form_fields));
        //second selected entry of manyToOne
        $this->assertThat($output, $this->stringContains("<option value=\"2\" selected>"));
    }

    public function test_post_form_validation_fails_existing_entry()
    {
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                   ->process([], ExampleEntityConstants::ENTRY_1_POST);
        $secondPostArray = [
            "value"          => "changed_value",
            "intcolumn"      => "invalidInt",
            "datecolumn"     => 'invalidDate',
            "datetimecolumn" => 'invalidDateTime',
            "wysiwygcolumn"  => BigTestValues::WYSIWYGVALUE2,
            "dropdowncolumn" => ExampleEntityDropdownValueSupplier::KEY_6,
            "manyToOne"      => ExampleEntityConstants::REFERENCED_ENTITY_ID_2,
            "manyToMany"     => [ExampleEntityConstants::REFERENCED_ENTITY_ID_1],
        ];
        ob_start();
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                   ->process([],
                                       $secondPostArray,
                                       1);


        $output = ob_get_clean();
        $form_fields = $secondPostArray;
        $form_fields['dropdowncolumn'] =
            "<option value=\"" . ExampleEntityDropdownValueSupplier::KEY_6 . "\" selected>";
        $form_fields['manyToOne'] = "<option value=\"1\" selected>";
        $form_fields['manyToMany'] = "<option value=\"1\" selected>";
        $this->assertThat($output, Matchers::stringContainsKeysAndValues($form_fields));
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
