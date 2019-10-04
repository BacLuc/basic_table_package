<?php


namespace BasicTablePackage\Test\Entity;


use BasicTablePackage\Entity\Repository;
use function BasicTablePackage\Lib\collect as collect;

class InMemoryRepository implements Repository
{
    /**
     * @var string
     */
    private $classname;
    /**
     * @var \Tightenco\Collect\Support\Collection of $classname
     */
    private $entites;

    public function __construct (string $classname)
    {
        $this->classname = $classname;
        $this->entites = collect([]);
    }

    public function create ()
    {
        return new $this->classname();
    }

    public function persist ($entity)
    {
        $this->entites = $this->entites->add($entity);
    }

    public function getAll ()
    {
        return $this->entites->toArray();
    }


}