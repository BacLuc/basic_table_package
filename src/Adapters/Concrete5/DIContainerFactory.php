<?php


namespace BasicTablePackage\Adapters\Concrete5;


use BasicTablePackage\Controller\ActionRegistry;
use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Controller\Renderer;
use BasicTablePackage\Controller\Validation\ValidationConfiguration;
use BasicTablePackage\Controller\Validation\ValidationConfigurationFactory;
use BasicTablePackage\Controller\ValuePersisters\PersistorConfiguration;
use BasicTablePackage\Controller\ValuePersisters\PersistorConfigurationFactory;
use BasicTablePackage\Controller\VariableSetter;
use BasicTablePackage\Entity\EntityManagerRepository;
use BasicTablePackage\Entity\ExampleEntity;
use BasicTablePackage\Entity\PersistenceFieldTypeReader;
use BasicTablePackage\Entity\Repository;
use BasicTablePackage\View\FormView\FormViewConfigurationFactory;
use BasicTablePackage\View\FormView\FormViewFieldConfiguration;
use BasicTablePackage\View\TableView\TableViewConfigurationFactory;
use BasicTablePackage\View\TableView\TableViewFieldConfiguration;
use BasicTablePackage\View\ViewActionRegistry;
use BasicTablePackage\View\ViewActionRegistryFactory;
use Concrete\Core\Block\BlockController;
use DI\Container;
use DI\ContainerBuilder;
use Doctrine\Common\Annotations\AnnotationRegistry;
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
    public static function createContainer (BlockController $controller,
                                            EntityManager $entityManager,
                                            $entityClass): Container
    {
        $containerBuilder = new ContainerBuilder();
        $definitions = self::createDefinition($entityManager, $entityClass);
        $definitions[BlockController::class] = value($controller);
        $containerBuilder->addDefinitions($definitions);
        return $containerBuilder->build();
    }

    /**
     * @param EntityManager $entityManager
     * @param $entityClass
     * @return array
     */
    public static function createDefinition (EntityManager $entityManager,
                                             $entityClass): array
    {
        AnnotationRegistry::registerLoader("class_exists");
        $definitions = [
            PersistenceFieldTypeReader::class  => value(new PersistenceFieldTypeReader($entityClass)),
            EntityManager::class               => value($entityManager),
            Repository::class                  => value(new EntityManagerRepository($entityManager,
                                                                                    ExampleEntity::class)),
            VariableSetter::class              => autowire(Concrete5VariableSetter::class),
            Renderer::class                    => autowire(Concrete5Renderer::class),
            ViewActionRegistry::class          => factory(function (Container $container) {
                return $container->get(ViewActionRegistryFactory::class)->createActionRegistry();
            }),
            ActionRegistry::class              => factory(function (Container $container) {
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
        return $definitions;
    }
}