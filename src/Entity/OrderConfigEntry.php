<?php


namespace BaclucC5Crud\Entity;


class OrderConfigEntry
{
    /**
     * @var string
     */
    private $sqlFieldName;

    /**
     * @var bool
     */
    private $asc;

    public function __construct(string $sqlFieldName, bool $asc = true)
    {
        $this->sqlFieldName = $sqlFieldName;
        $this->asc = $asc;
    }

    /**
     * @return string
     */
    public function getSqlFieldName()
    {
        return $this->sqlFieldName;
    }

    /**
     * @return bool
     */
    public function isAsc()
    {
        return $this->asc;
    }
}