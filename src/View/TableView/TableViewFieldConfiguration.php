<?php


namespace BaclucC5Crud\View\TableView;


use BaclucC5Crud\Lib\IteratorTrait;
use Iterator;

class TableViewFieldConfiguration implements Iterator
{
    use IteratorTrait;

    /**
     * TableViewFieldConfiguration constructor.
     * @param array $configuration as map of $sqlFieldName => callable ($value => Field)
     */
    public function __construct(array $configuration)
    {
        $this->initialize($configuration);
    }
}