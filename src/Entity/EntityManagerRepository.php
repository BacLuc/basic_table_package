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
    public function __construct(EntityManager $entityManager, string $className)
    {
        $this->entityManager = $entityManager;
        $this->className = $className;
    }

    public function create()
    {
        return new $this->className();
    }

    public function persist($entity)
    {
        $this->entityManager->transactional(function (EntityManager $em) use ($entity) {
            $em->persist($entity);
        });
    }

    public function getAll()
    {
        $query = $this->entityManager->createQuery(
        /** @lang DQL */
            "SELECT entity FROM $this->className entity");
        return $query->getResult();
    }

    public function getById(int $id)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $result = $qb->select("e")
                     ->from($this->className, "e")
                     ->where($qb->expr()->eq("e.id", ":id"))
                     ->setParameter(":id", $id)
                     ->getQuery()
                     ->getResult();
        return $result != null && is_array($result) && array_key_exists(0, $result) ? $result[0] : null;
    }

    public function delete($toDeleteEntity)
    {
        $this->entityManager->transactional(function (EntityManager $em) use ($toDeleteEntity) {
            $em->remove($toDeleteEntity);
        });
    }


}