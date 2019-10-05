<?php


namespace BasicTablePackage\Entity;


interface Repository
{
    public function create ();

    public function persist ($entity);

    /**
     * @return array of entities or null
     */
    public function getAll ();

    public function getById (int $id);
}