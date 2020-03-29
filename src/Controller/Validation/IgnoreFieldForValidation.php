<?php


namespace BaclucC5Crud\Controller\Validation;


class IgnoreFieldForValidation
{
    public static function create()
    {
        return function () {
            return null;
        };
    }
}