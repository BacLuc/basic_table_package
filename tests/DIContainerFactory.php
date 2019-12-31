<?php


namespace BasicTablePackage\Test;


use BasicTablePackage\Adapters\Concrete5\DIContainerFactory as ProductionDefinition;
use BasicTablePackage\Controller\Renderer;
use BasicTablePackage\Controller\VariableSetter;
use BasicTablePackage\Entity\EntityFieldOverrideBuilder;
use BasicTablePackage\Entity\ExampleEntity;
use BasicTablePackage\Entity\Repository;
use BasicTablePackage\Test\Adapters\DefaultContext;
use BasicTablePackage\Test\Adapters\DefaultRenderer;
use BasicTablePackage\Test\Adapters\DefaultWysiwygEditorFactory;
use BasicTablePackage\Test\Entity\InMemoryRepository;
use BasicTablePackage\View\FormView\WysiwygEditorFactory;
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