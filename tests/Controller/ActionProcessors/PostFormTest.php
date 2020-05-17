<?php

namespace BaclucC5Crud\Test\Controller\ActionProcessors;


use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Entity\ExampleEntity;
use BaclucC5Crud\Entity\ExampleEntityDropdownValueSupplier;
use BaclucC5Crud\Test\Constraints\Matchers;
use BaclucC5Crud\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class PostFormTest extends TestCase
{
    /**
     * @var CrudController
     */
    private $crudController;

    public function test_post_form_new_entry()
    {
        ob_start();
        $this->crudController->getActionFor(ActionRegistryFactory::POST_FORM, "0")
                             ->process([], ExampleEntityConstants::ENTRY_1_POST);
        $this->crudController->getActionFor(ActionRegistryFactory::SHOW_TABLE, "0")->process([], []);

        $output = ob_get_clean();
        $this->assertThat($output, Matchers::stringContainsKeysAndValues(ExampleEntityConstants::getValues()));
    }

    public function test_post_form_existing_entry()
    {
        $this->crudController->getActionFor(ActionRegistryFactory::POST_FORM, "0")
                                   ->process([], ExampleEntityConstants::ENTRY_1_POST);
        $secondPostArray = [
            "value"          => "changed_value",
            "intcolumn"      => 203498,
            "datecolumn"     => '13.12.2020',
            "datetimecolumn" => '13.12.2020 18:43',
            "wysiwygcolumn"  => BigTestValues::WYSIWYGVALUE2,
            "dropdowncolumn" => ExampleEntityDropdownValueSupplier::KEY_6,
            "manyToOne"      => ExampleEntityConstants::REFERENCED_ENTITY_ID_2,
            "manyToMany"     => [ExampleEntityConstants::REFERENCED_ENTITY_ID_1],
        ];
        $this->crudController->getActionFor(ActionRegistryFactory::POST_FORM, "0")
                             ->process([],
                                       $secondPostArray,
                                       1);

        ob_start();
        $this->crudController->getActionFor(ActionRegistryFactory::SHOW_TABLE, "0")->process([], []);

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
        $this->crudController->getActionFor(ActionRegistryFactory::POST_FORM, "0")
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
        $this->crudController->getActionFor(ActionRegistryFactory::POST_FORM, "0")
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
        $this->crudController->getActionFor(ActionRegistryFactory::POST_FORM, "0")
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
        $this->crudController->getActionFor(ActionRegistryFactory::POST_FORM, "0")
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
        $this->crudController = $container->get(CrudController::class);
    }
}
