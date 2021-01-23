<?php

namespace BaclucC5Crud\View\TableView;

class TableView {
    /**
     * @var string[]
     */
    private $headers;

    /**
     * @var Row[]
     */
    private $rows;
    /**
     * @var int
     */
    private $count;

    /**
     * TableView constructor.
     *
     * @param string[] $headers
     * @param Row[]    $rows
     */
    public function __construct(array $headers, array $rows, int $count) {
        $this->headers = $headers;
        $this->rows = $rows;
        $this->count = $count;
    }

    public static function empty() {
        return new TableView([], [], 0);
    }

    /**
     * @return string[]
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @return Row[]
     */
    public function getRows() {
        return $this->rows;
    }

    /**
     * @return int
     */
    public function getCount() {
        return $this->count;
    }
}
