<?php


namespace BasicTablePackage\Test\Controller\ActionProcessors;


class ExampleEntityConstants
{

    public const INT_VAL_1 = 65498;
    public const TEXT_VAL_1 = "test_value";
    public const DATE_VALUE_1 = '2020-12-12';
    const DATETIME_VALUE_1 = '2020-12-12 12:59';
    public const ENTRY_1_POST = [
        "value" => self::TEXT_VAL_1,
        "intcolumn" => self::INT_VAL_1,
        "datecolumn" => self::DATE_VALUE_1,
        "datetimecolumn" => self::DATETIME_VALUE_1
    ];
}