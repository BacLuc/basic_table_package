<?php


namespace BasicTablePackage\View\TableView;


use BasicTablePackage\Lib\IteratorTrait;
use Iterator;

class Row implements Iterator
{
    use IteratorTrait;

    /**
     * Row constructor.
     * @param String[] $values
     */
    public function __construct (array $values)
    {
        $this->initialize($values);
    }
}