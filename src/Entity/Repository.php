<?php


namespace BasicTablePackage\Entity;


interface Repository
{
    public function create ();

    public function persist ($entity);
}