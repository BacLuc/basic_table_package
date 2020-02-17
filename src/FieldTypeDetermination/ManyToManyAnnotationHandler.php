<?php


namespace BaclucC5Crud\FieldTypeDetermination;


use BaclucC5Crud\Entity\RepositoryFactory;
use BaclucC5Crud\Entity\RepositoryValueSupplier;
use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\ManyToMany;

class ManyToManyAnnotationHandler implements PersistenceFieldTypeHandler
{
    /**
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    public function __construct(RepositoryFactory $repositoryFactory)
    {
        $this->repositoryFactory = $repositoryFactory;
    }

    public function canHandle(Annotation $annotation): bool
    {
        return $annotation instanceof ManyToMany;
    }

    public function getFieldTypeOf(Annotation $annotation)
    {
        if ($annotation instanceof ManyToMany) {
            /** @var ManyToMany $annotation */
            $repository = $this->repositoryFactory->createRepositoryFor($annotation->targetEntity);
            $valueSupplier = new RepositoryValueSupplier($repository);
            return new ReferencingPersistenceFieldType(PersistenceFieldTypes::MANY_TO_MANY, $valueSupplier);
        } else {
            return null;
        }
    }
}