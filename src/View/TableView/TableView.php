<?php


namespace BaclucC5Crud\View\TableView;


class TableView
{
    /**
     * @var String[]
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
     * @param String[] $headers
     * @param Row[] $rows
     * @param int $count
     */
    public function __construct(array $headers, array $rows, int $count)
    {
        $this->headers = $headers;
        $this->rows = $rows;
        $this->count = $count;
    }

    public static function empty()
    {
        return new TableView([], [], 0);
    }

    /**
     * @return String[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return Row[]
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

}