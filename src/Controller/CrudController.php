<?php


namespace BaclucC5Crud\Controller;

use BaclucC5Crud\Controller\ActionProcessors\BlockIdAwareActionProcessor;

class CrudController
{
    /**
     * @var ActionRegistry
     */
    private $actionRegistry;
    /**
     * @var BlockIdSupplier
     */
    private $blockIdSupplier;

    public function __construct(ActionRegistry $actionRegistry, BlockIdSupplier $blockIdSupplier)
    {
        $this->actionRegistry = $actionRegistry;
        $this->blockIdSupplier = $blockIdSupplier;
    }

    public function getActionFor(string $string, string $blockIdOfRequest): ActionProcessor
    {
        return
            new BlockIdAwareActionProcessor($this->blockIdSupplier,
                $blockIdOfRequest,
                $this->actionRegistry->getByName($string),
                $this->actionRegistry->getByName(ActionRegistryFactory::SHOW_TABLE));
    }
}