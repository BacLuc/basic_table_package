<?php

namespace BaclucC5Crud\Test\Controller\ActionProcessors;

use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Entity\ExampleEntity;
use BaclucC5Crud\Test\Constraints\Matchers;
use BaclucC5Crud\Test\DIContainerFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShowNewEntryFormTest extends TestCase {
    /**
     * @var CrudController
     */
    private $crudController;

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function setUp() {
        /** @var EntityManager $entityManager */
        $entityManager = $this->createMock(EntityManager::class);
        /** @var Container $container */
        $container = DIContainerFactory::createContainer($entityManager, ExampleEntity::class);
        ExampleEntityConstants::addReferencedEntityTestValues($container);
        $this->crudController = $container->get(CrudController::class);
    }

    public function testNewRowFormHasEmptyFields() {
        ob_start();
        $this->crudController->getActionFor(ActionRegistryFactory::ADD_NEW_ROW_FORM, '0')
            ->process([], [])
        ;

        $output = ob_get_clean();
        $this->assertStringNotContainsString(ExampleEntityConstants::TEXT_VAL_1, $output);
        $this->assertStringNotContainsString(ExampleEntityConstants::INT_VAL_1, $output);
        $this->assertStringNotContainsString(ExampleEntityConstants::DATE_VALUE_1, $output);
        $this->assertStringNotContainsString(ExampleEntityConstants::DATETIME_VALUE_1, $output);
        $this->assertStringNotContainsString(ExampleEntityConstants::WYSIWYG_VALUE_1, $output);
        $this->assertStringContainsString('<option selected/>', $output);
        $this->assertStringContainsString(
            self::createOptionStringFor(ExampleEntityConstants::REFERENCED_ENTITY_ID_1),
            $output
        );
        $this->assertStringContainsString(
            self::createOptionStringFor(ExampleEntityConstants::REFERENCED_ENTITY_ID_2),
            $output
        );
        $this->assertThat($output, Matchers::stringContainsAll(array_keys(ExampleEntityConstants::ENTRY_1_POST)));
        $this->assertThat($output, $this->stringContains('action="post_form"'));
    }

    /**
     * @param $id
     */
    public static function createOptionStringFor($id): string {
        return '<option value="'.$id.'" >';
    }
}
