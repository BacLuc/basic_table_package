<?php


namespace BaclucC5Crud\Entity;


interface Repository
{
    public function create();

    public function persist($entity);

    /**
     * @param int $offset
     * @param int $limit
     * @return array of entities or null
     */
    public function getAll(int $offset = 0, int $limit = null);

    public function getById(int $id);

    public function delete($toDeleteEntity);

    public function count();
}