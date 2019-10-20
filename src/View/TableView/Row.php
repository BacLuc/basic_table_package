<?php


namespace BasicTablePackage\View\TableView;


use BasicTablePackage\Lib\IteratorTrait;
use Iterator;

class Row implements Iterator
{
    use IteratorTrait;
    private $id;

    /**
     * Row constructor.
     * @param int $id
     * @param String[] $values
     */
    public function __construct(int $id, array $values)
    {
        $this->id = $id;
        $this->initialize($values);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}