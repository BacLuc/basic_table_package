<?php


namespace BasicTablePackage\Test\Entity;


use BasicTablePackage\Entity\Repository;
use BasicTablePackage\Entity\RepositoryFactory;
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