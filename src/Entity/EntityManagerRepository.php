<?php


namespace BaclucC5Crud\Entity;


use Doctrine\ORM\EntityManager;

class EntityManagerRepository implements Repository, ConfigurationRepository
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

    public function getAll(int $offset = 0, int $limit = null)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $query = $qb->select("e")
                    ->from($this->className, "e")
                    ->setFirstResult($offset)
                    ->setMaxResults($limit)
                    ->getQuery();
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

    public function count()
    {
        $qb = $this->entityManager->createQueryBuilder();
        return $qb->select("count(e)")
                  ->from($this->className, "e")->getQuery()->getSingleScalarResult();
    }


}