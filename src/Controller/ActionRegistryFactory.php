<?php


namespace BasicTablePackage\Controller;


use BasicTablePackage\Controller\ActionProcessors\DeleteEntryActionProcessor;
use BasicTablePackage\Controller\ActionProcessors\PostFormActionProcessor;
use BasicTablePackage\Controller\ActionProcessors\ShowEditEntryFormActionProcessor;
use BasicTablePackage\Controller\ActionProcessors\ShowEntryDetailsActionProcessor;
use BasicTablePackage\Controller\ActionProcessors\ShowNewEntryFormActionProcessor;
use BasicTablePackage\Controller\ActionProcessors\ShowTableActionProcessor;

class ActionRegistryFactory
{
    const SHOW_TABLE = "show_table";
    const ADD_NEW_ROW_FORM = "add_new_row_form";
    const EDIT_ROW_FORM = "edit_row_form";
    const POST_FORM = "post_form";
    const DELETE_ENTRY = "delete_entry";
    const CANCEL_FORM = "cancel_form";
    const SHOW_ENTRY_DETAILS = "show_details";

    /**
     * @var ActionProcessor[]
     */
    private $actions;

    /**
     * ActionRegistryFactory constructor.
     * @param ShowTableActionProcessor $showTableActionProcessor
     * @param ShowNewEntryFormActionProcessor $showFormActionProcessor
     * @param PostFormActionProcessor $postFormActionProcessor
     * @param ShowEditEntryFormActionProcessor $showEditEntryFormActionProcessor
     * @param ShowEntryDetailsActionProcessor $showEntryDetailsActionProcessor
     */
    public function __construct(ShowTableActionProcessor $showTableActionProcessor,
                                ShowNewEntryFormActionProcessor $showFormActionProcessor,
                                PostFormActionProcessor $postFormActionProcessor,
                                ShowEditEntryFormActionProcessor $showEditEntryFormActionProcessor,
                                DeleteEntryActionProcessor $deleteEntryActionProcessor,
                                ShowEntryDetailsActionProcessor $showEntryDetailsActionProcessor)
    {
        $this->actions = [
            $showTableActionProcessor, $showFormActionProcessor, $postFormActionProcessor,
            $showEditEntryFormActionProcessor, $deleteEntryActionProcessor, $showEntryDetailsActionProcessor
        ];
    }


    public function createActionRegistry(): ActionRegistry
    {
        return new ActionRegistry($this->actions);
    }
}