<?php


namespace BasicTablePackage\Adapters\Concrete5;


use BasicTablePackage\Controller\Renderer;
use BasicTablePackage\Controller\VariableSetter;
use BasicTablePackage\View\FormView\FormViewConfigurationFactory;
use BasicTablePackage\View\FormView\FormViewFieldConfiguration;
use Concrete\Core\Block\BlockController;
use DI\Container;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use function DI\autowire;
use function DI\factory;
use function DI\value;

class DIContainerFactory
{
    /**
     * @param BlockController $controller
     * @return Container
     * @throws \Exception
     */
    public static function createContainer (BlockController $controller, EntityManager $entityManager): Container
    {
        $containerBuilder = new ContainerBuilder();
        $definitions = [
            EntityManager::class              => value($entityManager),
            BlockController::class            => value($controller),
            VariableSetter::class             => autowire(Concrete5VariableSetter::class),
            Renderer::class                   => autowire(Concrete5Renderer::class),
            FormViewFieldConfiguration::class => factory(function (Container $container) {
                return $container->get(FormViewConfigurationFactory::class)->createConfiguration();
            }),
        ];
        $containerBuilder->addDefinitions($definitions);
        return $containerBuilder->build();
    }
}