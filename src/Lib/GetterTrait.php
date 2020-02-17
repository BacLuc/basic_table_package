<?php


namespace BaclucC5Crud\Lib;


trait GetterTrait
{
    public function __get($name)
    {
        return $this->{$name};
    }
}