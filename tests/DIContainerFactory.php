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
use BasicTablePackage\Test\Adapters\DefaultContext;
use BasicTablePackage\Test\Adapters\DefaultRenderer;
use BasicTablePackage\View\FormView\FormViewConfigurationFactory;
use BasicTablePackage\View\FormView\FormViewFieldConfiguration;
use BasicTablePackage\View\ViewActionRegistry;
use BasicTablePackage\View\ViewActionRegistryFactory;
use DI\Container;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use function DI\autowire;
use function DI\factory;
use function DI\get;

class DIContainerFactory
{
    public static function createContainer (EntityManager $entityManager): Container
    {
        $containerBuilder = new ContainerBuilder();
        $definitions = [
            EntityManager::class              => value($entityManager),
            VariableSetter::class             => autowire(DefaultContext::class),
            DefaultContext::class             => get(VariableSetter::class),
            Renderer::class                   => autowire(DefaultRenderer::class),
            ViewActionRegistry::class         => factory(function (Container $container) {
                return $container->get(ViewActionRegistryFactory::class)->createActionRegistry();
            }),
            ActionRegistry::class             => factory(function (Container $container) {
                return $container->get(ActionRegistryFactory::class)->createActionRegistry();
            }),
            ValidationConfiguration::class    => factory(function (Container $container) {
                return $container->get(ValidationConfigurationFactory::class)->createConfiguration();
            }),
            FormViewFieldConfiguration::class => factory(function (Container $container) {
                return $container->get(FormViewConfigurationFactory::class)->createConfiguration();
            }),
            PersistorConfiguration::class     => factory(function (Container $container) {
                return $container->get(PersistorConfigurationFactory::class)->createConfiguration();
            }),
        ];
        $containerBuilder->addDefinitions($definitions);
        return $containerBuilder->build();
    }
}