<?php


namespace BasicTablePackage\View\TableView;


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


    public static function empty ()
    {
        return new TableView([], []);
    }

    /**
     * TableView constructor.
     * @param String[] $headers
     * @param Row[] $rows
     */
    public function __construct (array $headers, array $rows)
    {
        $this->headers = $headers;
        $this->rows = $rows;
    }

    /**
     * @return String[]
     */
    public function getHeaders ()
    {
        return $this->headers;
    }

    /**
     * @return Row[]
     */
    public function getRows ()
    {
        return $this->rows;
    }
}