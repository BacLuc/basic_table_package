<?php


namespace BasicTablePackage\Entity;

use function BasicTablePackage\Lib\collect as collect;

class RepositoryValueSupplier implements ValueSupplier
{
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    function getValues(): array
    {
        return collect($this->repository->getAll())
            ->keyBy(function ($value) {
                return $value->id;
            })
            ->toArray();
    }
}