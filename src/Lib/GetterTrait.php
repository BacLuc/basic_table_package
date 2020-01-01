<?php


namespace BasicTablePackage\Lib;


trait GetterTrait
{
    public function __get($name)
    {
        return $this->{$name};
    }
}