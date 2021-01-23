<?php

namespace BaclucC5Crud\View\TableView;

class DontShowTableField {
    public static function create(): \Closure {
        return function () {
            return function () {
                return null;
            };
        };
    }
}
