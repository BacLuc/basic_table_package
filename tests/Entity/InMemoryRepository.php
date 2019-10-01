<?php


namespace BasicTablePackage\Test\Entity;


use BasicTablePackage\Entity\Repository;

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
        // TODO: Implement persist() method.
    }
}