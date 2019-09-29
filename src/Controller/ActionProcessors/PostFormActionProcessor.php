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
     * @var Validator
     */
    private $validator;
    /**
     * @var ShowFormActionProcessor
     */
    private $showFormActionProcessor;

    /**
     * PostFormActionProcessor constructor.
     */
    public function __construct (ShowTableActionProcessor $showTableActionProcessor,
                                 Validator $validator,
                                 ShowFormActionProcessor $showFormActionProcessor)
    {
        $this->showTableActionProcessor = $showTableActionProcessor;
        $this->validator = $validator;
        $this->showFormActionProcessor = $showFormActionProcessor;
    }

    function getName (): string
    {
        return ActionRegistryFactory::POST_FORM;
    }

    function process (array $get, array $post)
    {
        $validationResult = $this->validator->validate($post);
        if (!$validationResult->isError()) {
            $this->showTableActionProcessor->process($get, $post);
        }
        else {
            $this->showFormActionProcessor->process($get, $post);
        }
    }

}