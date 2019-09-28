<?php


namespace BasicTablePackage\Controller\ActionProcessors;


use BasicTablePackage\Controller\ActionProcessor;
use BasicTablePackage\Controller\ActionRegistryFactory;

class PostFormActionProcessor implements ActionProcessor
{
    /**
     * @var ShowTableActionProcessor
     */
    private $showTableActionProcessor;

    /**
     * PostFormActionProcessor constructor.
     */
    public function __construct (ShowTableActionProcessor $showTableActionProcessor)
    {
        $this->showTableActionProcessor = $showTableActionProcessor;
    }

    function getName (): string
    {
        return ActionRegistryFactory::POST_FORM;
    }

    function process (array $get, array $post)
    {
        $this->showTableActionProcessor->process($get, $post);
    }

}