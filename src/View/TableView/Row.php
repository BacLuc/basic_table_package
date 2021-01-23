<?php

namespace BaclucC5Crud\View\TableView;

use BaclucC5Crud\Lib\IteratorTrait;
use Iterator;

class Row implements Iterator {
    use IteratorTrait;
    private $id;

    /**
     * Row constructor.
     *
     * @param string[] $values
     */
    public function __construct(int $id, array $values) {
        $this->id = $id;
        $this->initialize($values);
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }
}
