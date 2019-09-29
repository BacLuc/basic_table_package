<?php


namespace BasicTablePackage\Controller\ActionProcessors;


use BasicTablePackage\Lib\IteratorTrait;
use Iterator;

class ValidationConfiguration implements Iterator
{
    use IteratorTrait;

    /**
     * ValidationConfiguration constructor.
     */
    public function __construct (array $configuration)
    {
        $this->initialize($configuration);
    }
}