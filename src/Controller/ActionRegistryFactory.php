<?php


namespace BaclucC5Crud\Controller;


use BaclucC5Crud\Controller\ActionProcessors\DeleteEntry;
use BaclucC5Crud\Controller\ActionProcessors\PostForm;
use BaclucC5Crud\Controller\ActionProcessors\ShowEditEntryForm;
use BaclucC5Crud\Controller\ActionProcessors\ShowEntryDetails;
use BaclucC5Crud\Controller\ActionProcessors\ShowNewEntryForm;
use BaclucC5Crud\Controller\ActionProcessors\ShowTable;
use BaclucC5Crud\Controller\ActionProcessors\ValidateForm;

class ActionRegistryFactory
{
    const SHOW_TABLE         = "show_table";
    const ADD_NEW_ROW_FORM   = "add_new_row_form";
    const EDIT_ROW_FORM      = "edit_row_form";
    const POST_FORM          = "post_form";
    const VALIDATE_FORM      = "validate_form";
    const DELETE_ENTRY       = "delete_entry";
    const CANCEL_FORM        = "cancel_form";
    const SHOW_ENTRY_DETAILS = "show_details";

    /**
     * @var ActionProcessor[]
     */
    private $actions;

    public function __construct(
        ShowTable $showTableActionProcessor,
        ShowNewEntryForm $showFormActionProcessor,
        PostForm $postFormActionProcessor,
        ValidateForm $validateForm,
        ShowEditEntryForm $showEditEntryFormActionProcessor,
        DeleteEntry $deleteEntryActionProcessor,
        ShowEntryDetails $showEntryDetailsActionProcessor
    ) {
        $this->actions = func_get_args();
    }


    public function createActionRegistry(): ActionRegistry
    {
        return new ActionRegistry($this->actions);
    }
}