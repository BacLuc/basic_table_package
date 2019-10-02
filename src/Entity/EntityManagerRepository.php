<?php


namespace BasicTablePackage\Entity;


use Doctrine\ORM\EntityManager;

class EntityManagerRepository implements Repository
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var string
     */
    private $className;

    /**
     * EntityManagerRepository constructor.
     */
    public function __construct (EntityManager $entityManager, string $className)
    {
        $this->entityManager = $entityManager;
        $this->className = $className;
    }

    public function create ()
    {
        return new $this->className();
    }

    public function persist ($entity)
    {
        $this->entityManager->transactional(function (EntityManager $em) use ($entity) {
            $em->persist($entity);
        });
    }

    public function getAll ()
    {
        $query = $this->entityManager->createQuery(
        /** @lang DQL */
            "SELECT entity FROM $this->className entity");
        return $query->getResult();
    }
}