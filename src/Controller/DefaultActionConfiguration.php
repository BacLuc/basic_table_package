<?php


namespace BaclucC5Crud\Controller;


use BaclucC5Crud\View\ViewActionRegistry;

class DefaultActionConfiguration implements ActionConfiguration
{
    /**
     * @var ViewActionRegistry
     */
    private $viewActionRegistry;

    public function __construct(ViewActionRegistry $viewActionRegistry)
    {
        $this->viewActionRegistry = $viewActionRegistry;
    }


    /**
     * @inheritDoc
     */
    public function getActions(): array
    {
        return [$this->viewActionRegistry->getByName(ActionRegistryFactory::ADD_NEW_ROW_FORM)];
    }
}