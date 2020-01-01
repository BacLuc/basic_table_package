<?php


namespace BasicTablePackage\Lib;


trait SetterTrait
{
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }
}