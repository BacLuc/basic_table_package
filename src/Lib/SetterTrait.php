<?php


namespace BaclucC5Crud\Lib;


trait SetterTrait
{
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }
}