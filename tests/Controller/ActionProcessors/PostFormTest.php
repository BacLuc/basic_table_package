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
        $changed_value = "changed_value";
        $changed_int_value = 203498;
        $changed_date_value = '2020-12-13';
        $changed_date_time_value = '2020-12-13 18:43';
        $changed_wysiwyg_value = BigTestValues::WYSIWYGVALUE2;
        $changed_dropdown_value = ExampleEntityDropdownValueSupplier::KEY_6;
        $changed_manyToOne_value = ExampleEntityConstants::REFERENCED_ENTITY_ID_2;
        $this->basicTableController->getActionFor(ActionRegistryFactory::POST_FORM)
                                   ->process([],
                                       [
                                           "value"          => $changed_value,
                                           "intcolumn"      => $changed_int_value,
                                           "datecolumn"     => $changed_date_value,
                                           "datetimecolumn" => $changed_date_time_value,
                                           "wysiwygcolumn"  => $changed_wysiwyg_value,
                                           "dropdowncolumn" => $changed_dropdown_value,
                                           "manyToOne"      => $changed_manyToOne_value,
                                       ],
                                       1);

        $this->basicTableController->getActionFor(ActionRegistryFactory::SHOW_TABLE)->process([], []);

        $output = ob_get_clean();
        $this->assertStringNotContainsString(ExampleEntityConstants::TEXT_VAL_1, $output);
        $this->assertThat($output, $this->stringContains("value"));
        $this->assertThat($output, $this->stringContains($changed_value));
        $this->assertThat($output, $this->stringContains("intcolumn"));
        $this->assertThat($output, $this->stringContains($changed_int_value));
        $this->assertThat($output, $this->stringContains("datecolumn"));
        $this->assertThat($output, $this->stringContains($changed_date_value));
        $this->assertThat($output, $this->stringContains("datetimecolumn"));
        $this->assertThat($output, $this->stringContains($changed_date_time_value));
        $this->assertThat($output, $this->stringContains("wysiwygcolumn"));
        $this->assertThat($output, $this->stringContains($changed_wysiwyg_value));
        $this->assertThat($output, $this->stringContains("dropdowncolumn"));
        $exampleEntityDropdownValueSupplier = new ExampleEntityDropdownValueSupplier();
        $this->assertThat($output,
            $this->stringContains($exampleEntityDropdownValueSupplier->getValues()[$changed_dropdown_value]));
        $referencedEntityValues = ExampleEntityConstants::getReferencedEntityValues();
        $this->assertThat($output, $this->stringContains($referencedEntityValues[$changed_manyToOne_value]));
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
