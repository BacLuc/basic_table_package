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
        $this->assertStringNotContainsString(ExampleEntityConstants::WYSIWYG_VALUE_1, $output);
        $this->assertStringContainsString("<option selected/>", $output);
        $this->assertStringContainsString(self::createOptionStringFor(ExampleEntityConstants::REFERENCED_ENTITY_ID_1),
            $output);
        $this->assertStringContainsString(self::createOptionStringFor(ExampleEntityConstants::REFERENCED_ENTITY_ID_2),
            $output);
        $this->assertThat($output, Matchers::stringContainsAll(array_keys(ExampleEntityConstants::ENTRY_1_POST)));
        $this->assertThat($output, $this->stringContains("action=\"post_form\""));
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

    /**
     * @param $id
     * @return string
     */
    public static function createOptionStringFor($id): string
    {
        return "<option value=\"" . $id . "\" >";
    }
}
