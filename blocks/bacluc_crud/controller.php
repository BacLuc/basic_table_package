<?php

namespace Concrete\Package\BaclucC5Crud\Block\BaclucCrud;

use BaclucC5Crud\Adapters\Concrete5\Concrete5BlockConfigController;
use BaclucC5Crud\Adapters\Concrete5\Concrete5CrudController;
use BaclucC5Crud\Adapters\Concrete5\DIContainerFactory;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Controller\Validation\DropdownFieldValidator;
use BaclucC5Crud\Controller\Validation\FieldValidator;
use BaclucC5Crud\Entity\ExampleConfigurationEntity;
use BaclucC5Crud\Entity\ExampleEntity;
use BaclucC5Crud\Entity\ExampleEntityDropdownValueSupplier;
use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrideBuilder;
use BaclucC5Crud\View\FormType;
use BaclucC5Crud\View\FormView\DropdownField;
use BaclucC5Crud\View\FormView\Field as FormField;
use BaclucC5Crud\View\TableView\DropdownField as DropdownTableField;
use BaclucC5Crud\View\TableView\Field as TableField;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Support\Facade\Application;
use Concrete\Package\BaclucC5Crud\Controller as PackageController;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;

class Controller extends BlockController {
    use Concrete5BlockConfigController;
    use Concrete5CrudController;

    public function __construct($obj = null) {
        parent::__construct($obj);
        $this->initializeConfig($this, [$this, 'createConfigController'], $this->bID);
        $this->initializeCrud($this, [$this, 'createCrudController'], $this->bID);
    }

    public function getBlockTypeName() {
        return 'bacluc_crud';
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    private function createCrudController(): CrudController {
        $entityManager = PackageController::getEntityManagerStatic();
        $entityClass = ExampleEntity::class;
        $entityFieldOverrides = new EntityFieldOverrideBuilder($entityClass);

        $dropdownField = 'dropdowncolumn';
        $valueSupplier = new ExampleEntityDropdownValueSupplier();
        $entityFieldOverrides->forField($dropdownField)
            ->forType(FormField::class)
            ->useFactory(DropdownField::createDropdownField($dropdownField, $valueSupplier))
            ->forType(TableField::class)
            ->useFactory(DropdownTableField::createDropdownField($valueSupplier))
            ->forType(FieldValidator::class)
            ->useFactory(DropdownFieldValidator::createDropdownFieldValidator($valueSupplier))
            ->buildField()
        ;

        $app = Application::getFacadeApplication();
        /** @var PackageController $packageController */
        $packageController = $app->make(PackageService::class)->getByHandle(PackageController::PACKAGE_HANDLE);

        $container = DIContainerFactory::createContainer(
            $this,
            $entityManager,
            $entityClass,
            ExampleConfigurationEntity::class,
            $entityFieldOverrides->build(),
            $this->bID,
            $packageController->getPackagePath(),
            FormType::$BLOCK_VIEW
        );

        return $container->get(CrudController::class);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    private function createConfigController(): CrudController {
        $entityManager = PackageController::getEntityManagerStatic();
        $entityClass = ExampleConfigurationEntity::class;
        $entityFieldOverrides = new EntityFieldOverrideBuilder($entityClass);

        $dropdownField = 'dropdowncolumn';
        $valueSupplier = new ExampleEntityDropdownValueSupplier();
        $entityFieldOverrides->forField($dropdownField)
            ->forType(FormField::class)
            ->useFactory(DropdownField::createDropdownField($dropdownField, $valueSupplier))
            ->forType(TableField::class)
            ->useFactory(DropdownTableField::createDropdownField($valueSupplier))
            ->forType(FieldValidator::class)
            ->useFactory(DropdownFieldValidator::createDropdownFieldValidator($valueSupplier))
            ->buildField()
        ;

        $app = Application::getFacadeApplication();
        /** @var PackageController $packageController */
        $packageController = $app->make(PackageService::class)->getByHandle(PackageController::PACKAGE_HANDLE);
        $container = DIContainerFactory::createContainer(
            $this,
            $entityManager,
            $entityClass,
            '',
            $entityFieldOverrides->build(),
            $this->bID,
            $packageController->getPackagePath(),
            FormType::$BLOCK_CONFIGURATION
        );

        return $container->get(CrudController::class);
    }
}
