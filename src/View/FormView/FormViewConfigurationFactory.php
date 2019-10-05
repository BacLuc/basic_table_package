<?php


namespace BasicTablePackage\View\FormView;


class FormViewConfigurationFactory
{

    /**
     * TableViewConfigurationFactory constructor.
     */
    public function __construct () { }

    public function createConfiguration (): FormViewFieldConfiguration
    {
        return new FormViewFieldConfiguration([ "value" => function ($entity) {
            return new TextField("value", "value", $entity->value);
        },
                                              ]);
    }
}