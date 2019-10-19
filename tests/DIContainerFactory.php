<?php


namespace BasicTablePackage\Test;


use BasicTablePackage\Controller\ActionRegistry;
use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Controller\Renderer;
use BasicTablePackage\Controller\Validation\ValidationConfiguration;
use BasicTablePackage\Controller\Validation\ValidationConfigurationFactory;
use BasicTablePackage\Controller\ValuePersisters\PersistorConfiguration;
use BasicTablePackage\Controller\ValuePersisters\PersistorConfigurationFactory;
use BasicTablePackage\Controller\VariableSetter;
use BasicTablePackage\Entity\ExampleEntity;
use BasicTablePackage\Entity\PersistenceFieldTypeReader;
use BasicTablePackage\Entity\Repository;
use BasicTablePackage\Test\Adapters\DefaultContext;
use BasicTablePackage\Test\Adapters\DefaultRenderer;
use BasicTablePackage\Test\Entity\InMemoryRepository;
use BasicTablePackage\View\FormView\FormViewConfigurationFactory;
use BasicTablePackage\View\FormView\FormViewFieldConfiguration;
use BasicTablePackage\View\TableView\TableViewConfigurationFactory;
use BasicTablePackage\View\TableView\TableViewFieldConfiguration;
use BasicTablePackage\View\ViewActionRegistry;
use BasicTablePackage\View\ViewActionRegistryFactory;
use DI\Container;
use DI\ContainerBuilder;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use function DI\autowire;
use function DI\factory;
use function DI\get;

class DIContainerFactory
{
    public static function createContainer (EntityManager $entityManager, string $entityClass): Container
    {
        AnnotationRegistry::registerLoader("class_exists");
        $containerBuilder = new ContainerBuilder();
        $definitions = [
            PersistenceFieldTypeReader::class => value(new PersistenceFieldTypeReader($entityClass)),
            EntityManager::class              => value($entityManager),
            VariableSetter::class             => autowire(DefaultContext::class),
            DefaultContext::class             => get(VariableSetter::class),
            Renderer::class                   => autowire(DefaultRenderer::class),
            Repository::class                 => value(new InMemoryRepository(ExampleEntity::class)),
            ViewActionRegistry::class         => factory(function (Container $container) {
                return $container->get(ViewActionRegistryFactory::class)->createActionRegistry();
            }),
            ActionRegistry::class             => factory(function (Container $container) {
                return $container->get(ActionRegistryFactory::class)->createActionRegistry();
            }),
            TableViewFieldConfiguration::class => factory(function (Container $container) {
                return $container->get(TableViewConfigurationFactory::class)->createConfiguration();
            }),
            ValidationConfiguration::class     => factory(function (Container $container) {
                return $container->get(ValidationConfigurationFactory::class)->createConfiguration();
            }),
            FormViewFieldConfiguration::class  => factory(function (Container $container) {
                return $container->get(FormViewConfigurationFactory::class)->createConfiguration();
            }),
            PersistorConfiguration::class      => factory(function (Container $container) {
                return $container->get(PersistorConfigurationFactory::class)->createConfiguration();
            }),
        ];
        $containerBuilder->addDefinitions($definitions);
        return $containerBuilder->build();
    }
}