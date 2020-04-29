<?php


namespace BaclucC5Crud\Entity;


interface TableViewEntrySupplier
{
    public function getEntries();

    public function count();
}