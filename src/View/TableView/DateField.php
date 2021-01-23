<?php

namespace BaclucC5Crud\View\TableView;

use DateTime;

class DateField implements Field {
    /**
     * @var null|DateTime
     */
    private $sqlValue;

    /**
     * TextField constructor.
     */
    public function __construct(?DateTime $sqlValue) {
        $this->sqlValue = $sqlValue;
    }

    public function getTableView(): string {
        return $this->sqlValue ? $this->sqlValue->format('d.m.Y') : '';
    }
}
