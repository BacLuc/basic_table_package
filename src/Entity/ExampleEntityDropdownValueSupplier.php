<?php

namespace BaclucC5Crud\Entity;

use BaclucC5Crud\Lib\ValueSupplierTrait;

class ExampleEntityDropdownValueSupplier implements ValueSupplier {
    use ValueSupplierTrait;

    const KEY_5 = 'five';
    const KEY_6 = 'six';
    const VALUE_5 = 'dropdownvalue5';
    const DROPDOWN_VALUE_6 = 'dropdownvalue6';

    public function __construct() {
        $this->initialize([
            'zero' => 'dropdownvalue0',
            'one' => 'dropdownvalue1',
            self::KEY_5 => self::VALUE_5,
            self::KEY_6 => self::DROPDOWN_VALUE_6,
        ]);
    }
}
