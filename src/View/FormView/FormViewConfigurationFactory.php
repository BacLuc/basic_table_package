<?php


namespace BasicTablePackage\View\FormView;


class FormViewConfigurationFactory
{

    /**
     * TableViewConfigurationFactory constructor.
     */
    public function __construct()
    {
    }

    public function createConfiguration(): FormViewFieldConfiguration
    {
        return new FormViewFieldConfiguration([
            "value" => function ($entity) {
                $fieldName = "value";
                return new TextField("value", "value", property_exists($entity, $fieldName) ? $entity->{$fieldName} : null);
            },
            "intcolumn" => function ($entity) {
                $fieldName = "intcolumn";
                return new IntegerField($fieldName, $fieldName, property_exists($entity, $fieldName) ? $entity->{$fieldName} : null);
            },
            "datecolumn" => function ($entity) {
                $fieldName = "datecolumn";
                return new DateField($fieldName, $fieldName, property_exists($entity, $fieldName) ? $entity->{$fieldName} : null);
            }
        ]);
    }
}