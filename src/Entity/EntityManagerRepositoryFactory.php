<?php

namespace BaclucC5Crud\Entity;

use Doctrine\ORM\EntityManager;
use InvalidArgumentException;

class EntityManagerRepositoryFactory implements RepositoryFactory {
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function createRepositoryFor(string $className): Repository {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("class {$className} does not exist");
        }

        return new EntityManagerRepository($this->entityManager, $className);
    }
}
