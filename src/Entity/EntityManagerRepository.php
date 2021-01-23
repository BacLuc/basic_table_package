<?php

namespace BaclucC5Crud\Entity;

use Doctrine\ORM\EntityManager;

class EntityManagerRepository implements Repository, ConfigurationRepository {
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
    public function __construct(EntityManager $entityManager, string $className) {
        $this->entityManager = $entityManager;
        $this->className = $className;
    }

    public function create() {
        return new $this->className();
    }

    public function persist(Identifiable $entity) {
        $this->entityManager->transactional(function (EntityManager $em) use ($entity) {
            $em->persist($entity);
        });
    }

    public function getAll(int $offset = 0, int $limit = null, array $orderEntries = []) {
        $qb = $this->entityManager->createQueryBuilder();
        $unorderedQuery = $qb->select('e')
            ->from($this->className, 'e')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        /** @var OrderConfigEntry $entry */
        foreach ($orderEntries as $entry) {
            $unorderedQuery = $unorderedQuery->addOrderBy($entry->getSqlFieldName(), $entry->isAsc() ? 'ASC' : 'DESC');
        }
        $query = $unorderedQuery
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function getById(int $id) {
        $qb = $this->entityManager->createQueryBuilder();
        $idFieldName = call_user_func($this->className.'::getIdFieldName');
        $result = $qb->select('e')
            ->from($this->className, 'e')
            ->where($qb->expr()->eq('e.'.$idFieldName, ':id'))
            ->setParameter(':id', $id)
            ->getQuery()
            ->getResult()
        ;

        return null != $result && is_array($result) && array_key_exists(0, $result) ? $result[0] : null;
    }

    public function delete(Identifiable $toDeleteEntity) {
        $this->entityManager->transactional(function (EntityManager $em) use ($toDeleteEntity) {
            $em->remove($toDeleteEntity);
        });
    }

    public function count() {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('count(e)')
            ->from($this->className, 'e')->getQuery()->getSingleScalarResult();
    }
}
