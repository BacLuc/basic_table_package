<?php


namespace BasicTablePackage\Controller\Validation;


class ValidationConfigurationFactory
{
    public function __construct () { }

    public function createConfiguration (): ValidationConfiguration
    {
        return new ValidationConfiguration([
                                               new TextFieldValidator("value"),
                                           ]);
    }
}