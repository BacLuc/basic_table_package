<?php


namespace BaclucC5Crud\Controller\ActionProcessors;


use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\Validation\Validator;

class ValidateForm implements ActionProcessor
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * PostFormActionProcessor constructor.
     * @param Validator $validator
     */
    public function __construct(
        Validator $validator
    ) {
        $this->validator = $validator;
    }

    function getName(): string
    {
        return ActionRegistryFactory::VALIDATE_FORM;
    }

    function process(array $get, array $post, ...$additionalParameters)
    {
        return $this->validator->validate($post);
    }

}