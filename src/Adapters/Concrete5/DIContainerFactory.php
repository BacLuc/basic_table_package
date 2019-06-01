<?php


namespace BasicTablePackage\Adapters\Concrete5;


use BasicTablePackage\Controller\Renderer;
use BasicTablePackage\Controller\VariableSetter;
use Concrete\Core\Block\BlockController;
use DI\Container;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use function DI\autowire;
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
        $containerBuilder->addDefinitions([
                                              EntityManager::class   => value($entityManager),
                                              BlockController::class => value($controller),
                                              VariableSetter::class  => autowire(Concrete5VariableSetter::class),
                                              Renderer::class        => autowire(Concrete5Renderer::class),
                                          ]);
        return $containerBuilder->build();
    }
}