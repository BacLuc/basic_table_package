<?php


namespace BasicTablePackage\Controller\Validation;


use BasicTablePackage\Lib\IteratorTrait;
use Iterator;

class ValidationConfiguration implements Iterator
{
    use IteratorTrait;

    /**
     * ValidationConfiguration constructor.
     */
    public function __construct(array $configuration)
    {
        $this->initialize($configuration);
    }
}