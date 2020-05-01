<?php


namespace BaclucC5Crud\Adapters\Concrete5;


use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Controller\Validation\ValidationResult;
use BaclucC5Crud\Controller\Validation\ValidationResultItem;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Error\ErrorList\ErrorList;
use DI\DependencyException;
use DI\NotFoundException;

trait Concrete5BlockConfigController
{
    /**
     * @var BlockController
     */
    private $blockController;
    /**
     * @var CrudController
     */
    private $crudController;

    private $blockId;

    private function initializeConfig($blockController, $crudController, $blockId)
    {
        $this->blockController = $blockController;
        $this->crudController = $crudController;
        $this->blockId = $blockId;
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function add()
    {
        ProcessAction::processAction($this->blockController,
            $this->crudController
                ->getActionFor(ActionRegistryFactory::ADD_NEW_ROW_FORM, $this->blockId));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function edit()
    {
        ProcessAction::processAction($this->blockController,
            $this->crudController
                ->getActionFor(ActionRegistryFactory::EDIT_ROW_FORM, $this->blockId));
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
        $validationResult = ProcessAction::processAction($this->blockController,
            $this->crudController
                ->getActionFor(ActionRegistryFactory::VALIDATE_FORM, $this->blockId),
            $this->blockId);
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
        ProcessAction::processAction($this->blockController,
            $this->crudController
                ->getActionFor(ActionRegistryFactory::POST_FORM, $this->blockId),
            $this->blockId);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function delete()
    {
        parent::delete();
        ProcessAction::processAction($this->blockController,
            $this->crudController
                ->getActionFor(ActionRegistryFactory::DELETE_ENTRY, $this->blockId),
            $this->blockId);
    }
}