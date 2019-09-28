<?php


namespace BasicTablePackage\Controller;


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