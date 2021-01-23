<?php

namespace BaclucC5Crud\Entity;

use BaclucC5Crud\Controller\PaginationConfiguration;

class AllValuesTableViewEntrySupplier implements TableViewEntrySupplier {
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(Repository $repository) {
        $this->repository = $repository;
    }

    public function getEntries(PaginationConfiguration $paginationConfiguration) {
        return $this->repository->getAll(
            $paginationConfiguration->getOffset(),
            $paginationConfiguration->getPageSize()
        );
    }

    public function count() {
        return $this->repository->count();
    }
}
