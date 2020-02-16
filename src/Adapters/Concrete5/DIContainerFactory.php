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
use BasicTablePackage\Entity\EntityManagerRepositoryFactory;
use BasicTablePackage\Entity\Repository;
use BasicTablePackage\Entity\RepositoryFactory;
use BasicTablePackage\FieldConfigurationOverride\EntityFieldOverrides;
use BasicTablePackage\FieldTypeDetermination\ColumnAnnotationHandler;
use BasicTablePackage\FieldTypeDetermination\ManyToManyAnnotationHandler;
use BasicTablePackage\FieldTypeDetermination\ManyToOneAnnotationHandler;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypeReader;
use BasicTablePackage\View\FormType;
use BasicTablePackage\View\FormView\FormViewConfigurationFactory;
use BasicTablePackage\View\FormView\FormViewFieldConfiguration;
use BasicTablePackage\View\FormView\ValueTransformers\PersistenceValueTransformerConfiguration;
use BasicTablePackage\View\FormView\ValueTransformers\ValueTransformerConfiguration;
use BasicTablePackage\View\FormView\WysiwygEditorFactory;
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
     * @param EntityManager $entityManager
     * @param $entityClass
     * @param EntityFieldOverrides $entityFieldOverrides
     * @param FormType $formType
     * @return Container
     * @throws \Exception
     */
    public static function createContainer(
        BlockController $controller,
        EntityManager $entityManager,
        $entityClass,
        EntityFieldOverrides $entityFieldOverrides,
        FormType $formType = null
    ): Container {
        $containerBuilder = new ContainerBuilder();
        $definitions = self::createDefinition($entityManager, $entityClass, $entityFieldOverrides, $formType);
        $definitions[BlockController::class] = value($controller);
        $containerBuilder->addDefinitions($definitions);
        return $containerBuilder->build();
    }

    /**
     * @param EntityManager $entityManager
     * @param $entityClass
     * @param EntityFieldOverrides $entityFieldOverrides
     * @param FormType|null $formType
     * @return array
     */
    public static function createDefinition(
        EntityManager $entityManager,
        $entityClass,
        EntityFieldOverrides $entityFieldOverrides,
        FormType $formType = null
    ): array {
        $formType = $formType ? $formType : FormType::$BLOCK_VIEW;
        AnnotationRegistry::registerLoader("class_exists");
        $definitions = [
            PersistenceFieldTypeReader::class    => factory(function (Container $container) use ($entityClass) {
                return new PersistenceFieldTypeReader($entityClass,
                    [
                        new ColumnAnnotationHandler(),
                        new ManyToOneAnnotationHandler($container->get(RepositoryFactory::class)),
                        new ManyToManyAnnotationHandler($container->get(RepositoryFactory::class))
                    ]);
            }),
            EntityManager::class                 => value($entityManager),
            Repository::class                    => value(new EntityManagerRepository($entityManager,
                $entityClass)),
            EntityFieldOverrides::class          => value($entityFieldOverrides),
            VariableSetter::class                => autowire(Concrete5VariableSetter::class),
            Renderer::class                      => autowire(Concrete5Renderer::class),
            ViewActionRegistry::class            => factory(function (Container $container) {
                return $container->get(ViewActionRegistryFactory::class)->createActionRegistry();
            }),
            ActionRegistry::class                => factory(function (Container $container) {
                return $container->get(ActionRegistryFactory::class)->createActionRegistry();
            }),
            TableViewFieldConfiguration::class   => factory(function (Container $container) {
                return $container->get(TableViewConfigurationFactory::class)->createConfiguration();
            }),
            ValidationConfiguration::class       => factory(function (Container $container) {
                return $container->get(ValidationConfigurationFactory::class)->createConfiguration();
            }),
            FormViewFieldConfiguration::class    => factory(function (Container $container) {
                return $container->get(FormViewConfigurationFactory::class)->createConfiguration();
            }),
            PersistorConfiguration::class        => factory(function (Container $container) {
                return $container->get(PersistorConfigurationFactory::class)->createConfiguration();
            }),
            WysiwygEditorFactory::class          => value(new Concrete5WysiwygEditorFactory()),
            RepositoryFactory::class             => value(new EntityManagerRepositoryFactory($entityManager)),
            ValueTransformerConfiguration::class => autowire(PersistenceValueTransformerConfiguration::class),
            FormType::class                      => value($formType)

        ];
        return $definitions;
    }
}