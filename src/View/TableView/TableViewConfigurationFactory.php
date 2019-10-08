<?php


namespace BasicTablePackage\View\TableView;


class TableViewConfigurationFactory
{

    /**
     * TableViewConfigurationFactory constructor.
     */
    public function __construct () { }

    public function createConfiguration (): TableViewFieldConfiguration
    {
        return new TableViewFieldConfiguration([
            "value" => function ($value) {
                return new TextField($value);
            },
            "intcolumn" => function ($value) {
                return new TextField($value);
            }
        ]);
    }
}