<?php


namespace BasicTablePackage\View\TableView;


class TextField implements Field
{
    /**
     * @var string
     */
    private $sqlValue;

    /**
     * TextField constructor.
     */
    public function __construct (string $sqlValue)
    {
        $this->sqlValue = $sqlValue;
    }

    public function getTableView (): string
    {
        return $this->sqlValue;
    }


}