<?php


namespace BasicTablePackage\Test\Controller\ActionProcessors;


use BasicTablePackage\Entity\ExampleEntityDropdownValueSupplier;

class ExampleEntityConstants
{

    public const INT_VAL_1        = 65498;
    public const TEXT_VAL_1       = "test_value";
    public const DATE_VALUE_1     = '2020-12-12';
    const        DATETIME_VALUE_1 = '2020-12-12 12:59';
    const        WYSIWYG_VALUE_1  = BigTestValues::WYSIWYGVALUE;
    const        DROPDOWN_KEY_5   = ExampleEntityDropdownValueSupplier::KEY_5;
    const        DROPDOWN_VALUE_5 = ExampleEntityDropdownValueSupplier::VALUE_5;
    public const ENTRY_1_POST     = [
        "value"          => self::TEXT_VAL_1,
        "intcolumn"      => self::INT_VAL_1,
        "datecolumn"     => self::DATE_VALUE_1,
        "datetimecolumn" => self::DATETIME_VALUE_1,
        "wysiwygcolumn"  => self::WYSIWYG_VALUE_1,
        "dropdowncolumn" => self::DROPDOWN_KEY_5,
    ];

    public static function getValues()
    {
        $postValues = self::ENTRY_1_POST;
        $exampleEntityDropdownValueSupplier = new ExampleEntityDropdownValueSupplier();
        $dropDownValues = $exampleEntityDropdownValueSupplier->getValues();
        $postValues["dropdowncolumn"] = $dropDownValues[self::ENTRY_1_POST["dropdowncolumn"]];
        return $postValues;
    }
}