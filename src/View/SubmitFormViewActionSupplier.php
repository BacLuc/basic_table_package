<?php


namespace BaclucC5Crud\View;


use BaclucC5Crud\Controller\ActionRegistryFactory;

class SubmitFormViewActionSupplier
{
    /**
     * @var ViewActionRegistry
     */
    private $viewActionRegistry;

    public function __construct(ViewActionRegistry $viewActionRegistry)
    {
        $this->viewActionRegistry = $viewActionRegistry;
    }

    public function getAction(): ViewActionDefinition
    {
        return $this->viewActionRegistry->getByName(ActionRegistryFactory::POST_FORM);
    }
}