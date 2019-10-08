<?php


namespace BasicTablePackage\View\FormView;


class FormViewConfigurationFactory
{

    /**
     * TableViewConfigurationFactory constructor.
     */
    public function __construct () { }

    public function createConfiguration(): FormViewFieldConfiguration
    {
        return new FormViewFieldConfiguration(["value" => function ($entity) {
            $fieldName = "value";
            return new TextField("value", "value", property_exists($entity, $fieldName) ? $entity->{$fieldName} : null);
        },
        ]);
    }
}