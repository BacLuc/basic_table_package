<?php

namespace BaclucC5Crud\Adapters\Concrete5;

use BaclucC5Crud\Controller\ActionRegistryFactory;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;

trait Concrete5CrudController {
    /**
     * @var BlockController
     */
    private $blockController;
    /**
     * @var callable
     */
    private $crudController;

    private $blockId;

    public function view() {
        ProcessAction::processAction(
            $this->blockController,
            call_user_func($this->crudController)
                ->getActionFor(ActionRegistryFactory::SHOW_TABLE, $this->blockId)
        );
    }

    public function action_add_new_row_form($blockId) {
        ProcessAction::processAction(
            $this->blockController,
            call_user_func($this->crudController)
                ->getActionFor(ActionRegistryFactory::ADD_NEW_ROW_FORM, $blockId)
        );
    }

    public function action_edit_row_form($blockId, $editId) {
        ProcessAction::processAction(
            $this->blockController,
            call_user_func($this->crudController)
                ->getActionFor(ActionRegistryFactory::EDIT_ROW_FORM, $blockId),
            $editId
        );
    }

    public function action_post_form($blockId, $editId = null) {
        ProcessAction::processAction(
            $this->blockController,
            call_user_func($this->crudController)
                ->getActionFor(ActionRegistryFactory::POST_FORM, $blockId),
            $editId
        );
        if (null == $this->blockController->blockViewRenderOverride) {
            Redirect::page(Page::getCurrentPage())->send();

            exit();
        }
    }

    public function action_delete_entry($blockId, $toDeleteId) {
        ProcessAction::processAction(
            $this->blockController,
            call_user_func($this->crudController)
                ->getActionFor(ActionRegistryFactory::DELETE_ENTRY, $blockId),
            $toDeleteId
        );
        if (null == $this->blockViewRenderOverride) {
            Redirect::page(Page::getCurrentPage())->send();

            exit();
        }
    }

    public function action_cancel_form($blockId) {
        ProcessAction::processAction(
            $this->blockController,
            call_user_func($this->crudController)
                ->getActionFor(ActionRegistryFactory::SHOW_TABLE, $blockId)
        );
    }

    public function action_show_details($blockId, $toShowId) {
        ProcessAction::processAction(
            $this->blockController,
            call_user_func($this->crudController)
                ->getActionFor(ActionRegistryFactory::SHOW_ENTRY_DETAILS, $blockId),
            $toShowId
        );
    }

    private function initializeCrud($blockController, callable $crudControllerSupplier, $blockId) {
        $this->blockController = $blockController;
        $this->crudController = $crudControllerSupplier;
        $this->blockId = $blockId;
    }
}
