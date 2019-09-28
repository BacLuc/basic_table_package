<?php


namespace BasicTablePackage\Controller;

class BasicTableController
{
    /**
     * @var ActionRegistry
     */
    private $actionRegistry;

    public function __construct (ActionRegistry $actionRegistry)
    {
        $this->actionRegistry = $actionRegistry;
    }

    public function view(){
        $this->actionRegistry->getByName(ActionRegistryFactory::SHOW_TABLE)->process([], []);
    }

    public function openForm (int $editId = null)
    {
        $this->actionRegistry->getByName(ActionRegistryFactory::ADD_NEW_ROW_FORM)->process([], []);
    }

    public function getActionFor (string $string): ActionProcessor
    {
        return $this->actionRegistry->getByName($string);
    }
}