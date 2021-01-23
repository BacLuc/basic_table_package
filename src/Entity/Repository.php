<?php

namespace BaclucC5Crud\Entity;

interface Repository {
    public function create();

    public function persist(Identifiable $entity);

    /**
     * @param int $limit
     *
     * @return array of entities or null
     */
    public function getAll(int $offset = 0, int $limit = null, array $orderEntries = []);

    public function getById(int $id);

    public function delete(Identifiable $toDeleteEntity);

    public function count();
}
