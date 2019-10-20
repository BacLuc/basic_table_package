<?php


namespace BasicTablePackage\View;


use BasicTablePackage\Controller\ActionRegistryFactory;

class ViewActionRegistryFactory
{

    public function createActionRegistry(): ViewActionRegistry
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
            new ViewActionDefinition(ActionRegistryFactory::DELETE_ENTRY,
                "delete",
                "Delete Entry",
                "Delete Entry",
                "fa fa-trash"),
            new ViewActionDefinition(ActionRegistryFactory::SHOW_ENTRY_DETAILS,
                "details",
                "Show Entry Details",
                "Show Entry Details",
                "fa fa-search-plus"),
        ];
        return new ViewActionRegistry($actions);
    }
}