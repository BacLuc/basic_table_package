<?php


namespace BaclucC5Crud\View\FormView;


class DontShowFormField
{
    public static function create(): \Closure
    {
        return function () {
            return function () {
                return null;
            };
        };
    }
}