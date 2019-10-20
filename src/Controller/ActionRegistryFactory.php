<?php


namespace BasicTablePackage\Controller;


use BasicTablePackage\Controller\ActionProcessors\DeleteEntry;
use BasicTablePackage\Controller\ActionProcessors\PostForm;
use BasicTablePackage\Controller\ActionProcessors\ShowEditEntryForm;
use BasicTablePackage\Controller\ActionProcessors\ShowEntryDetails;
use BasicTablePackage\Controller\ActionProcessors\ShowNewEntryForm;
use BasicTablePackage\Controller\ActionProcessors\ShowTable;

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
     * @param ShowTable $showTableActionProcessor
     * @param ShowNewEntryForm $showFormActionProcessor
     * @param PostForm $postFormActionProcessor
     * @param ShowEditEntryForm $showEditEntryFormActionProcessor
     * @param ShowEntryDetails $showEntryDetailsActionProcessor
     */
    public function __construct(ShowTable $showTableActionProcessor,
                                ShowNewEntryForm $showFormActionProcessor,
                                PostForm $postFormActionProcessor,
                                ShowEditEntryForm $showEditEntryFormActionProcessor,
                                DeleteEntry $deleteEntryActionProcessor,
                                ShowEntryDetails $showEntryDetailsActionProcessor)
    {
        $this->actions = [
            $showTableActionProcessor, $showFormActionProcessor, $postFormActionProcessor,
            $showEditEntryFormActionProcessor, $deleteEntryActionProcessor, $showEntryDetailsActionProcessor,
        ];
    }


    public function createActionRegistry(): ActionRegistry
    {
        return new ActionRegistry($this->actions);
    }
}