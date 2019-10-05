<?php


namespace BasicTablePackage\Controller;


use BasicTablePackage\Controller\ActionProcessors\PostFormActionProcessor;
use BasicTablePackage\Controller\ActionProcessors\ShowFormActionProcessor;
use BasicTablePackage\Controller\ActionProcessors\ShowTableActionProcessor;

class ActionRegistryFactory
{
    const SHOW_TABLE       = "show_table";
    const ADD_NEW_ROW_FORM = "add_new_row_form";
    const EDIT_ROW_FORM    = "edit_row_form";
    const POST_FORM        = "post_form";
    const CANCEL_FORM      = "cancel_form";

    /**
     * @var ActionProcessor[]
     */
    private $actions;

    /**
     * ActionRegistryFactory constructor.
     * @param ShowTableActionProcessor $showTableActionProcessor
     * @param ShowFormActionProcessor $showFormActionProcessor
     * @param PostFormActionProcessor $postFormActionProcessor
     */
    public function __construct (ShowTableActionProcessor $showTableActionProcessor,
                                 ShowFormActionProcessor $showFormActionProcessor,
                                 PostFormActionProcessor $postFormActionProcessor)
    {
        $this->actions = [
            $showTableActionProcessor, $showFormActionProcessor, $postFormActionProcessor,
        ];
    }


    public function createActionRegistry (): ActionRegistry
    {
        return new ActionRegistry($this->actions);
    }
}