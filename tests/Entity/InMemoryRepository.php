<?php


namespace BasicTablePackage\Test\Entity;


use BasicTablePackage\Entity\Repository;
use function BasicTablePackage\Lib\collect as collect;

class InMemoryRepository implements Repository
{
    /**
     * @var int
     */
    private $autoIncrement = 0;
    /**
     * @var string
     */
    private $classname;
    /**
     * @var \Tightenco\Collect\Support\Collection of $classname
     */
    private $entites;

    public function __construct(string $classname)
    {
        $this->classname = $classname;
        $this->entites = collect([]);
    }

    public function create()
    {
        return new $this->classname();
    }

    public function persist($entity)
    {
        $foundEntity = $this->entites->first(function ($persistedEntity) use ($entity) {
            return $persistedEntity->id === $entity->id;
        });
        if ($foundEntity != null) {
            return;
        }

        if ($entity->id == null) {
            $entity->id = ++$this->autoIncrement;
        }
        $this->entites = $this->entites->add($entity);
    }

    public function getAll()
    {
        return $this->entites->toArray();
    }

    public function getById(int $id)
    {
        return $this->entites->first(function ($entity) use ($id) {
            return $entity->id === $id;
        });
    }

    public function delete($toDeleteEntity)
    {
        $this->entites = $this->entites->filter(function ($entity) use ($toDeleteEntity) {
            return $entity->id !== $toDeleteEntity->id;
        });
    }

}