<?php


namespace BasicTablePackage\Test;


use BasicTablePackage\Controller\Renderer;
use BasicTablePackage\Controller\VariableSetter;
use BasicTablePackage\Test\Adapters\DefaultContext;
use BasicTablePackage\Test\Adapters\DefaultRenderer;
use DI\Container;
use DI\ContainerBuilder;
use function DI\autowire;
use function DI\get;

class DIContainerFactory
{
    public static function createContainer (): Container
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
                                              VariableSetter::class => autowire(DefaultContext::class),
                                              DefaultContext::class => get(VariableSetter::class),
                                              Renderer::class       => autowire(DefaultRenderer::class),
                                          ]);
        return $containerBuilder->build();
    }
}