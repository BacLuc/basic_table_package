<?php


namespace BasicTablePackage\Test\Entity;


use BasicTablePackage\Entity\Repository;
use BasicTablePackage\Entity\RepositoryFactory;
use InvalidArgumentException;

class InMemoryRepositoryFactory implements RepositoryFactory
{

    public function createRepositoryFor(string $className): Repository
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("class $className does not exist");
        }
        return new InMemoryRepository($className);
    }
}