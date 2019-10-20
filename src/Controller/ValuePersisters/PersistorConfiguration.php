<?php


namespace BasicTablePackage\Controller\ValuePersisters;


use BasicTablePackage\Lib\IteratorTrait;
use Iterator;

class PersistorConfiguration implements Iterator
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