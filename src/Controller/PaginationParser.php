<?php


namespace BaclucC5Crud\Controller;


class PaginationParser
{
    public function parse(array $get): PaginationConfiguration
    {
        $currentPage = $this->getAsInt($get, 'currentPage', 0);
        $pageSize = $this->getAsInt($get, 'pageSize', 10);
        return new PaginationConfiguration($currentPage, $pageSize);
    }

    private function getAsInt(array $get, $key, $default)
    {
        if (!isset($get[$key])) {
            return $default;
        }
        return filter_var($get[$key], FILTER_VALIDATE_INT) !== false ?
            intval($get[$key]) :
            $default;
    }
}