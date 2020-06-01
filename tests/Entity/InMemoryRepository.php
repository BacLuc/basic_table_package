<?php


namespace BaclucC5Crud\Test\Entity;


use BaclucC5Crud\Entity\ConfigurationRepository;
use BaclucC5Crud\Entity\Identifiable;
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

    public function persist(Identifiable $entity)
    {
        $foundEntity = $this->entites->first(function (Identifiable $persistedEntity) use ($entity) {
            return $persistedEntity->getId() === $entity->getId();
        });
        if ($foundEntity != null) {
            return;
        }

        if ($entity->getId() == null) {
            $entity->setId(++$this->autoIncrement);
        }
        $this->entites = $this->entites->add($entity);
    }

    public function getAll(int $offset = 0, int $limit = null, array $orderEntries = [])
    {
        return $this->entites->slice($offset, $limit)->toArray();
    }

    public function getById(int $id)
    {
        return $this->entites->first(function (Identifiable $entity) use ($id) {
            return $entity->getId() === $id;
        });
    }

    public function delete(Identifiable $toDeleteEntity)
    {
        $this->entites = $this->entites->filter(function (Identifiable $entity) use ($toDeleteEntity) {
            return $entity->getId() !== $toDeleteEntity->getId();
        });
    }

    public function count()
    {
        return $this->entites->count();
    }
}