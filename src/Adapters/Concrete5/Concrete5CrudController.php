<?php


namespace BaclucC5Crud\Adapters\Concrete5;


use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\CrudController;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;

trait Concrete5CrudController
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

    private function initializeCrud($blockController, $crudController, $blockId)
    {
        $this->blockController = $blockController;
        $this->crudController = $crudController;
        $this->blockId = $blockId;
    }

    public function view()
    {
        ProcessAction::processAction($this->blockController,
            $this->crudController
                ->getActionFor(ActionRegistryFactory::SHOW_TABLE, $this->blockId));
    }

    public function action_add_new_row_form($blockId)
    {
        ProcessAction::processAction($this->blockController,
            $this->crudController
                ->getActionFor(ActionRegistryFactory::ADD_NEW_ROW_FORM, $blockId));
    }

    public function action_edit_row_form($blockId, $editId)
    {
        ProcessAction::processAction($this->blockController,
            $this->crudController
                ->getActionFor(ActionRegistryFactory::EDIT_ROW_FORM, $blockId),
            $editId);
    }

    public function action_post_form($blockId, $editId = null)
    {
        ProcessAction::processAction($this->blockController,
            $this->crudController
                ->getActionFor(ActionRegistryFactory::POST_FORM, $blockId),
            $editId);
        if ($this->blockController->blockViewRenderOverride == null) {
            Redirect::page(Page::getCurrentPage())->send();
            exit();
        }
    }

    public function action_delete_entry($blockId, $toDeleteId)
    {
        ProcessAction::processAction($this->blockController,
            $this->crudController
                ->getActionFor(ActionRegistryFactory::DELETE_ENTRY, $blockId),
            $toDeleteId);
        if ($this->blockViewRenderOverride == null) {
            Redirect::page(Page::getCurrentPage())->send();
            exit();
        }
    }

    public function action_cancel_form($blockId)
    {
        ProcessAction::processAction($this->blockController,
            $this->crudController
                ->getActionFor(ActionRegistryFactory::SHOW_TABLE, $blockId));
    }

    public function action_show_details($blockId, $toShowId)
    {
        ProcessAction::processAction($this->blockController,
            $this->crudController
                ->getActionFor(ActionRegistryFactory::SHOW_ENTRY_DETAILS, $blockId),
            $toShowId);
    }

}