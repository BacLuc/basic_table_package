<?php


namespace BaclucC5Crud\Controller;


use BaclucC5Crud\View\ViewActionRegistry;

class DefaultRowActionConfiguration implements RowActionConfiguration
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
        return [
            $this->viewActionRegistry->getByName(ActionRegistryFactory::EDIT_ROW_FORM),
            $this->viewActionRegistry->getByName(ActionRegistryFactory::DELETE_ENTRY),
            $this->viewActionRegistry->getByName(ActionRegistryFactory::SHOW_ENTRY_DETAILS),
        ];
    }
}