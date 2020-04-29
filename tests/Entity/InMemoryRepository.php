<?php


namespace BaclucC5Crud\Test\Entity;


use BaclucC5Crud\Entity\ConfigurationRepository;
use BaclucC5Crud\Entity\Repository;
use function BaclucC5Crud\Lib\collect as collect;

class InMemoryRepository implements Repository, ConfigurationRepository
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

    public function getAll(int $offset = 0, int $limit = null)
    {
        return $this->entites->slice($offset, $limit)->toArray();
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

    public function count()
    {
        return $this->entites->count();
    }
}