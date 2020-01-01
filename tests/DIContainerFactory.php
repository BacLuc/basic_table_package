<?php


namespace BasicTablePackage\Test;


use BasicTablePackage\Adapters\Concrete5\DIContainerFactory as ProductionDefinition;
use BasicTablePackage\Controller\Renderer;
use BasicTablePackage\Controller\Validation\DropdownFieldValidator;
use BasicTablePackage\Controller\Validation\FieldValidator;
use BasicTablePackage\Controller\VariableSetter;
use BasicTablePackage\Entity\ExampleEntity;
use BasicTablePackage\Entity\ExampleEntityDropdownValueSupplier;
use BasicTablePackage\Entity\Repository;
use BasicTablePackage\FieldConfigurationOverride\EntityFieldOverrideBuilder;
use BasicTablePackage\Test\Adapters\DefaultContext;
use BasicTablePackage\Test\Adapters\DefaultRenderer;
use BasicTablePackage\Test\Adapters\DefaultWysiwygEditorFactory;
use BasicTablePackage\Test\Entity\InMemoryRepository;
use BasicTablePackage\View\FormView\Dropdownfield as DropdownFormField;
use BasicTablePackage\View\FormView\Field as FormField;
use BasicTablePackage\View\FormView\WysiwygEditorFactory;
use BasicTablePackage\View\TableView\DropdownField as DropdownTableField;
use BasicTablePackage\View\TableView\Field as TableField;
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

            $dropdownfield = "dropdowncolumn";
            $valueSupplier = new ExampleEntityDropdownValueSupplier();
            $entityFieldOverrides->forField($dropdownfield)
                                 ->forType(FormField::class)
                                 ->useFactory(DropdownFormField::createDropdownField($dropdownfield, $valueSupplier))
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
            $entityFieldOverrides->build());

        $definitions[VariableSetter::class] = autowire(DefaultContext::class);
        $definitions[DefaultContext::class] = get(VariableSetter::class);
        $definitions[Renderer::class] = autowire(DefaultRenderer::class);
        $definitions[Repository::class] = value(new InMemoryRepository(ExampleEntity::class));
        $definitions[WysiwygEditorFactory::class] = value(new DefaultWysiwygEditorFactory());

        $containerBuilder->addDefinitions($definitions);
        return $containerBuilder->build();
    }
}