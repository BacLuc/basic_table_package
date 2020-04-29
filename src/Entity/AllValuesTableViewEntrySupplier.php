<?php


namespace BaclucC5Crud\Entity;


class AllValuesTableViewEntrySupplier implements TableViewEntrySupplier
{
    /**
     * @var Repository
     */
    private $repository;


    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getEntries()
    {
        return $this->repository->getAll();
    }

    public function count()
    {
        return $this->repository->count();
    }
}