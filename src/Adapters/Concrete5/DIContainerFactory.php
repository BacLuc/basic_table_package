<?php


namespace BaclucC5Crud\Adapters\Concrete5;


use BaclucC5Crud\Controller\ActionRegistry;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\BlockIdSupplier;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\Validation\ValidationConfiguration;
use BaclucC5Crud\Controller\Validation\ValidationConfigurationFactory;
use BaclucC5Crud\Controller\ValuePersisters\PersistorConfiguration;
use BaclucC5Crud\Controller\ValuePersisters\PersistorConfigurationFactory;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\Entity\AllValuesTableViewEntrySupplier;
use BaclucC5Crud\Entity\ConfigurationRepository;
use BaclucC5Crud\Entity\EntityManagerRepositoryFactory;
use BaclucC5Crud\Entity\Repository;
use BaclucC5Crud\Entity\RepositoryFactory;
use BaclucC5Crud\Entity\TableViewEntrySupplier;
use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrides;
use BaclucC5Crud\FieldTypeDetermination\ColumnAnnotationHandler;
use BaclucC5Crud\FieldTypeDetermination\ManyToManyAnnotationHandler;
use BaclucC5Crud\FieldTypeDetermination\ManyToOneAnnotationHandler;
use BaclucC5Crud\FieldTypeDetermination\OneToManyAnnotationHandler;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypeReader;
use BaclucC5Crud\View\FormType;
use BaclucC5Crud\View\FormView\FormViewConfigurationFactory;
use BaclucC5Crud\View\FormView\FormViewFieldConfiguration;
use BaclucC5Crud\View\FormView\ValueTransformers\PersistenceValueTransformerConfiguration;
use BaclucC5Crud\View\FormView\ValueTransformers\ValueTransformerConfiguration;
use BaclucC5Crud\View\FormView\WysiwygEditorFactory;
use BaclucC5Crud\View\TableView\TableViewConfigurationFactory;
use BaclucC5Crud\View\TableView\TableViewFieldConfiguration;
use BaclucC5Crud\View\ViewActionRegistry;
use BaclucC5Crud\View\ViewActionRegistryFactory;
use Concrete\Core\Block\BlockController;
use DI\Container;
use DI\ContainerBuilder;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use function DI\autowire;
use function DI\create;
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
        $configurationClass,
        EntityFieldOverrides $entityFieldOverrides,
        $blockId,
        FormType $formType = null
    ): Container {
        $containerBuilder = new ContainerBuilder();
        $definitions =
            self::createDefinition($entityManager,
                $entityClass,
                $configurationClass,
                $entityFieldOverrides,
                $blockId,
                $formType);
        $definitions[BlockController::class] = value($controller);
        $containerBuilder->addDefinitions($definitions);
        return $containerBuilder->build();
    }

    public static function createDefinition(
        EntityManager $entityManager,
        $entityClass,
        $configurationClass,
        EntityFieldOverrides $entityFieldOverrides,
        $blockId,
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
                        new ManyToManyAnnotationHandler($container->get(RepositoryFactory::class)),
                        new OneToManyAnnotationHandler($container->get(RepositoryFactory::class))
                    ]);
            }),
            EntityManager::class                 => value($entityManager),
            Repository::class                    => factory([RepositoryFactory::class, "createRepositoryFor"])
                ->parameter('className', $entityClass),
            ConfigurationRepository::class       => factory([RepositoryFactory::class, "createRepositoryFor"])
                ->parameter('className', $configurationClass),
            EntityFieldOverrides::class          => value($entityFieldOverrides),
            VariableSetter::class                => autowire(Concrete5VariableSetter::class),
            Renderer::class                      => autowire(Concrete5Renderer::class),
            ViewActionRegistry::class            => factory([ViewActionRegistryFactory::class, "createActionRegistry"]),
            ActionRegistry::class                => factory([ActionRegistryFactory::class, "createActionRegistry"]),
            TableViewFieldConfiguration::class   => factory([
                TableViewConfigurationFactory::class,
                "createConfiguration"
            ]),
            ValidationConfiguration::class       => factory([
                ValidationConfigurationFactory::class,
                "createConfiguration"
            ]),
            FormViewFieldConfiguration::class    => factory([
                FormViewConfigurationFactory::class,
                "createConfiguration"
            ]),
            PersistorConfiguration::class        => factory([
                PersistorConfigurationFactory::class,
                "createConfiguration"
            ]),
            WysiwygEditorFactory::class          => value(new Concrete5WysiwygEditorFactory()),
            RepositoryFactory::class             => value(new EntityManagerRepositoryFactory($entityManager)),
            ValueTransformerConfiguration::class => autowire(PersistenceValueTransformerConfiguration::class),
            FormType::class                      => value($formType),
            TableViewEntrySupplier::class        => autowire(AllValuesTableViewEntrySupplier::class),
            BlockIdSupplier::class               => create()->constructor($blockId)

        ];
        return $definitions;
    }
}