<?php


namespace BaclucC5Crud\Test;


use BaclucC5Crud\Adapters\Concrete5\DIContainerFactory as ProductionDefinition;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\Validation\DropdownFieldValidator;
use BaclucC5Crud\Controller\Validation\FieldValidator;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\Entity\ExampleConfigurationEntity;
use BaclucC5Crud\Entity\ExampleEntity;
use BaclucC5Crud\Entity\ExampleEntityDropdownValueSupplier;
use BaclucC5Crud\Entity\Repository;
use BaclucC5Crud\Entity\RepositoryFactory;
use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrideBuilder;
use BaclucC5Crud\Test\Adapters\DefaultContext;
use BaclucC5Crud\Test\Adapters\DefaultRenderer;
use BaclucC5Crud\Test\Adapters\DefaultWysiwygEditorFactory;
use BaclucC5Crud\Test\Entity\InMemoryRepository;
use BaclucC5Crud\Test\Entity\InMemoryRepositoryFactory;
use BaclucC5Crud\View\FormView\DropdownField as DropdownFormField;
use BaclucC5Crud\View\FormView\Field as FormField;
use BaclucC5Crud\View\FormView\WysiwygEditorFactory;
use BaclucC5Crud\View\TableView\DropdownField as DropdownTableField;
use BaclucC5Crud\View\TableView\Field as TableField;
use DI\Container;
use DI\ContainerBuilder;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use ReflectionException;
use RuntimeException;
use function DI\autowire;
use function DI\get;

class DIContainerFactory
{
    public static function createContainer(EntityManager $entityManager, string $entityClass): Container
    {
        AnnotationRegistry::registerLoader("class_exists");
        $containerBuilder = new ContainerBuilder();
        try {
            $entityFieldOverrides = new EntityFieldOverrideBuilder($entityClass);

            $dropdownField = "dropdowncolumn";
            $valueSupplier = new ExampleEntityDropdownValueSupplier();
            $entityFieldOverrides->forField($dropdownField)
                                 ->forType(FormField::class)
                                 ->useFactory(DropdownFormField::createDropdownField($dropdownField, $valueSupplier))
                                 ->forType(TableField::class)
                                 ->useFactory(DropdownTableField::createDropdownField($valueSupplier))
                                 ->forType(FieldValidator::class)
                                 ->useFactory(DropdownFieldValidator::createDropdownFieldValidator($valueSupplier))
                                 ->buildField();
        } catch (ReflectionException $e) {
            throw new RuntimeException($e);
        }
        $definitions = ProductionDefinition::createDefinition(
            $entityManager,
            $entityClass,
            ExampleConfigurationEntity::class,
            $entityFieldOverrides->build(),
            "0");

        $definitions[VariableSetter::class] = autowire(DefaultContext::class);
        $definitions[DefaultContext::class] = get(VariableSetter::class);
        $definitions[Renderer::class] = autowire(DefaultRenderer::class);
        $definitions[Repository::class] = value(new InMemoryRepository(ExampleEntity::class));
        $definitions[WysiwygEditorFactory::class] = value(new DefaultWysiwygEditorFactory());
        $definitions[RepositoryFactory::class] = value(new InMemoryRepositoryFactory());

        $containerBuilder->addDefinitions($definitions);
        return $containerBuilder->build();
    }
}