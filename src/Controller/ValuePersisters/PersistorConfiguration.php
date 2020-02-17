<?php


namespace BaclucC5Crud\Controller\ValuePersisters;


use BaclucC5Crud\Lib\IteratorTrait;
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