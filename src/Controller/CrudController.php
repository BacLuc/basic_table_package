<?php


namespace BaclucC5Crud\Controller;

use BaclucC5Crud\Controller\ActionProcessors\BlockIdAwareActionProcessor;

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

    public function getActionFor(string $string, string $blockIdOfBlock, string $blockIdOfRequest): ActionProcessor
    {
        return
            new BlockIdAwareActionProcessor($blockIdOfBlock,
                $blockIdOfRequest,
                $this->actionRegistry->getByName($string),
                $this->actionRegistry->getByName(ActionRegistryFactory::SHOW_TABLE));
    }
}