<?php

namespace BaclucC5Crud\Controller\Validation;

use BaclucC5Crud\Lib\IteratorTrait;
use Iterator;

class ValidationConfiguration implements Iterator {
    use IteratorTrait;

    /**
     * ValidationConfiguration constructor.
     */
    public function __construct(array $configuration) {
        $this->initialize($configuration);
    }
}
