<?php


namespace BasicTablePackage\Controller\ActionProcessors;


use function BasicTablePackage\Lib\collect as collect;

class Validator
{
    /**
     * @var ValidationConfiguration
     */
    private $validationConfiguration;

    public function __construct (ValidationConfiguration $validationConfiguration)
    {
        $this->validationConfiguration = $validationConfiguration;
    }

    public function validate ($post): ValidationResult
    {
        return new ValidationResult(collect($this->validationConfiguration)
                                        ->map(function (FieldValidator $fieldValidator) use ($post
                                        ) {
                                            return $fieldValidator->validate($post);
                                        })
                                        ->toArray());
    }

}