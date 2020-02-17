<?php


namespace BaclucC5Crud\Test\Entity;


use BaclucC5Crud\Entity\Repository;
use BaclucC5Crud\Entity\RepositoryFactory;
use InvalidArgumentException;

class InMemoryRepositoryFactory implements RepositoryFactory
{
    private $createdRepositories = [];

    public function createRepositoryFor(string $className): Repository
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("class $className does not exist");
        }
        if (!isset($this->createdRepositories[$className])) {
            $this->createdRepositories[$className] = new InMemoryRepository($className);
        }
        return $this->createdRepositories[$className];
    }
}