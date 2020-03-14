<?php


namespace BaclucC5Crud\Controller\ActionProcessors;


use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\BlockIdSupplier;

class BlockIdAwareActionProcessor implements ActionProcessor
{
    /**
     * @var string
     */
    private $blockIdOfBlock;
    /**
     * @var string
     */
    private $blockIdOfRequest;
    /**
     * @var ActionProcessor
     */
    private $successAction;
    /**
     * @var ActionProcessor
     */
    private $failAction;
    /**
     * @var BlockIdSupplier
     */
    private $blockIdSupplier;


    public function __construct(
        BlockIdSupplier $blockIdSupplier,
        string $blockIdOfRequest,
        ActionProcessor $successAction,
        ActionProcessor $failAction
    ) {
        $this->blockIdSupplier = $blockIdSupplier;
        $this->blockIdOfRequest = $blockIdOfRequest;
        $this->successAction = $successAction;
        $this->failAction = $failAction;
    }

    function getName(): string
    {
        return $this->successAction->getName();
    }

    function process(array $get, array $post, ...$additionalParameters)
    {
        $args = func_get_args();
        if ($this->blockIdSupplier->getBlockId() === $this->blockIdOfRequest) {
            return call_user_func_array([$this->successAction, "process"], $args);
        } else {
            return call_user_func_array([$this->failAction, "process"], $args);
        }
    }
}