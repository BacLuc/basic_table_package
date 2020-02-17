<?php


namespace BaclucC5Crud\Controller;

class CrudController
{
    /**
     * @var ActionRegistry
     */
    private $actionRegistry;

    public function __construct(ActionRegistry $actionRegistry)
    {
        $this->actionRegistry = $actionRegistry;
    }

    public function getActionFor(string $string): ActionProcessor
    {
        return $this->actionRegistry->getByName($string);
    }
}