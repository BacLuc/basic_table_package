<?php


namespace BaclucC5Crud\View\TableView;


use DateTime;

class DateField implements Field
{
    /**
     * @var DateTime|null
     */
    private $sqlValue;

    /**
     * TextField constructor.
     * @param DateTime|null $sqlValue
     */
    public function __construct(?DateTime $sqlValue)
    {
        $this->sqlValue = $sqlValue;
    }

    public function getTableView(): string
    {
        return $this->sqlValue ? $this->sqlValue->format('Y-m-d') : "";
    }
}