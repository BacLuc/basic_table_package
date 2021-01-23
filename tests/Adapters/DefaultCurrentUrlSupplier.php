<?php

namespace BaclucC5Crud\Test\Adapters;

use BaclucC5Crud\Controller\CurrentUrlSupplier;

class DefaultCurrentUrlSupplier implements CurrentUrlSupplier {
    public function getUrl() {
        return 'test';
    }
}
