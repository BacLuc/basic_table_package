<?php

namespace BaclucC5Crud\View\TableView;

class TextField implements Field {
    /**
     * @var string
     */
    private $sqlValue;

    /**
     * TextField constructor.
     *
     * @param mixed $sqlValue
     */
    public function __construct($sqlValue) {
        $this->sqlValue = $sqlValue;
    }

    public function getTableView(): string {
        return $this->sqlValue ?: '';
    }
}
