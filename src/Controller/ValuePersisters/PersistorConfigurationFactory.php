<?php


namespace BasicTablePackage\Controller\ValuePersisters;


class PersistorConfigurationFactory
{
    public function __construct()
    {
    }

    public function createConfiguration(): PersistorConfiguration
    {
        return new PersistorConfiguration([
            new TextFieldPersistor("value"),
            new TextFieldPersistor("intcolumn"),
            new DatePersistor("datecolumn")
        ]);
    }
}