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
        ];
        return new ViewActionRegistry($actions);
    }
}