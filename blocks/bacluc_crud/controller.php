<?php

namespace Concrete\Package\BaclucC5Crud\Block\BaclucCrud;

use BaclucC5Crud\Adapters\Concrete5\DIContainerFactory;
use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Controller\Validation\DropdownFieldValidator;
use BaclucC5Crud\Controller\Validation\FieldValidator;
use BaclucC5Crud\Controller\Validation\ValidationResult;
use BaclucC5Crud\Controller\Validation\ValidationResultItem;
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
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;
use Concrete\Package\BaclucC5Crud\Controller as PackageController;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;

class Controller extends BlockController
{

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function view()
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::SHOW_TABLE, $this->bID));
    }

    private function processAction(ActionProcessor $actionProcessor, ...$additionalParams)
    {
        return $actionProcessor->process($this->request->get(null) ?: [],
            $this->request->post(null) ?: [],
            array_key_exists(0, $additionalParams) ? $additionalParams[0] : null);
    }

    /**
     * @return CrudController
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    private function createCrudController(): CrudController
    {
        $entityManager = PackageController::getEntityManagerStatic();
        $entityClass = ExampleEntity::class;
        $entityFieldOverrides = new EntityFieldOverrideBuilder($entityClass);

        $dropdownField = "dropdowncolumn";
        $valueSupplier = new ExampleEntityDropdownValueSupplier();
        $entityFieldOverrides->forField($dropdownField)
                             ->forType(FormField::class)
                             ->useFactory(DropdownField::createDropdownField($dropdownField, $valueSupplier))
                             ->forType(TableField::class)
                             ->useFactory(DropdownTableField::createDropdownField($valueSupplier))
                             ->forType(FieldValidator::class)
                             ->useFactory(DropdownFieldValidator::createDropdownFieldValidator($valueSupplier))
                             ->buildField();

        $container = DIContainerFactory::createContainer($this,
            $entityManager,
            $entityClass,
            $entityFieldOverrides->build(),
            $this->bID,
            FormType::$BLOCK_VIEW);
        return $container->get(CrudController::class);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_add_new_row_form($blockId)
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::ADD_NEW_ROW_FORM, $blockId));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_edit_row_form($blockId, $editId)
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::EDIT_ROW_FORM, $blockId),
            $editId);
    }

    /**
     * Attention: all action method are called twice.
     * Because this is a form submission, we stop after the function is executed
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_post_form($blockId, $editId = null)
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::POST_FORM, $blockId),
            $editId);
        if ($this->blockViewRenderOverride == null) {
            Redirect::page(Page::getCurrentPage())->send();
            exit();
        }
    }

    /**
     * @param $ignored
     * @param $toDeleteId
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_delete_entry($blockId, $toDeleteId)
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::DELETE_ENTRY, $blockId),
            $toDeleteId);
        if ($this->blockViewRenderOverride == null) {
            Redirect::page(Page::getCurrentPage())->send();
            exit();
        }
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_cancel_form($blockId)
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::SHOW_TABLE, $blockId));
    }

    /**
     * @param $ignored
     * @param $toShowId
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_show_details($blockId, $toShowId)
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::SHOW_ENTRY_DETAILS, $blockId),
            $toShowId);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function add()
    {
        $this->processAction($this->createConfigController()
                                  ->getActionFor(ActionRegistryFactory::ADD_NEW_ROW_FORM, $this->bID));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function edit()
    {
        $this->processAction($this->createConfigController()
                                  ->getActionFor(ActionRegistryFactory::EDIT_ROW_FORM, $this->bID),
            $this->bID);
    }

    /**
     * @param array|string|null $args
     * @return bool|ErrorList|void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function validate($args)
    {

        /** @var $validationResult ValidationResult */
        $validationResult = $this->processAction($this->createConfigController()
                                                      ->getActionFor(ActionRegistryFactory::VALIDATE_FORM,
                                                          $this->bID),
            $this->bID);
        /** @var $e ErrorList */
        $e = $this->app->make(ErrorList::class);
        foreach ($validationResult as $validationResultItem) {
            /** @var $validationResultItem ValidationResultItem */
            foreach ($validationResultItem->getMessages() as $message) {
                $e->add($validationResultItem->getName() . ": " . $message,
                    $validationResultItem->getName(),
                    $validationResultItem->getName());
            }
        }
        return $e;
    }

    /**
     * @param array $args
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function save($args)
    {
        parent::save($args);
        $this->processAction($this->createConfigController()
                                  ->getActionFor(ActionRegistryFactory::POST_FORM, $this->bID),
            $this->bID);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function delete()
    {
        parent::delete();
        $this->processAction($this->createConfigController()
                                  ->getActionFor(ActionRegistryFactory::DELETE_ENTRY, $this->bID),
            $this->bID);
    }

    /**
     * @return CrudController
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    private function createConfigController(): CrudController
    {
        $entityManager = PackageController::getEntityManagerStatic();
        $entityClass = ExampleConfigurationEntity::class;
        $entityFieldOverrides = new EntityFieldOverrideBuilder($entityClass);

        $dropdownField = "dropdowncolumn";
        $valueSupplier = new ExampleEntityDropdownValueSupplier();
        $entityFieldOverrides->forField($dropdownField)
                             ->forType(FormField::class)
                             ->useFactory(DropdownField::createDropdownField($dropdownField, $valueSupplier))
                             ->forType(TableField::class)
                             ->useFactory(DropdownTableField::createDropdownField($valueSupplier))
                             ->forType(FieldValidator::class)
                             ->useFactory(DropdownFieldValidator::createDropdownFieldValidator($valueSupplier))
                             ->buildField();

        $container = DIContainerFactory::createContainer($this,
            $entityManager,
            $entityClass,
            $entityFieldOverrides->build(),
            $this->bID,
            FormType::$BLOCK_CONFIGURATION);
        return $container->get(CrudController::class);
    }

    public function getBlockTypeName()
    {
        return "bacluc_crud";
    }


}