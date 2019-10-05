<?php


namespace BasicTablePackage\View;


use BasicTablePackage\Controller\ActionRegistryFactory;

class ViewActionRegistryFactory
{

    public function createActionRegistry (): ViewActionRegistry
    {
        $actions = [
            new ViewActionDefinition(ActionRegistryFactory::ADD_NEW_ROW_FORM,
                                     "add",
                                     "new Entry",
                                     "new Entry",
                                     "fa-plus"),
            new ViewActionDefinition(ActionRegistryFactory::POST_FORM, "", "submit", "submit", ""),
            new ViewActionDefinition(ActionRegistryFactory::CANCEL_FORM, "", "cancel", "cancel", ""),
            new ViewActionDefinition(ActionRegistryFactory::EDIT_ROW_FORM,
                                     "edit",
                                     "Edit Entry",
                                     "Edit Entry",
                                     "fa fa-pencil"),
        ];
        return new ViewActionRegistry($actions);
    }
}