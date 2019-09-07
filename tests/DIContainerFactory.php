<?php


namespace BasicTablePackage\Test;


use BasicTablePackage\Controller\Renderer;
use BasicTablePackage\Controller\VariableSetter;
use BasicTablePackage\Test\Adapters\DefaultContext;
use BasicTablePackage\Test\Adapters\DefaultRenderer;
use BasicTablePackage\View\FormView\FormViewConfigurationFactory;
use BasicTablePackage\View\FormView\FormViewFieldConfiguration;
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
            FormViewFieldConfiguration::class => factory(function (Container $container) {
                return $container->get(FormViewConfigurationFactory::class)->createConfiguration();
            }),
        ];
        $containerBuilder->addDefinitions($definitions);
        return $containerBuilder->build();
    }
}