<?php


namespace BasicTablePackage\FieldTypeDetermination;


use BasicTablePackage\Entity\RepositoryFactory;
use BasicTablePackage\Entity\RepositoryValueSupplier;
use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\ManyToOne;

class ManyToOneAnnotationHandler implements PersistenceFieldTypeHandler
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
        return $annotation instanceof ManyToOne;
    }

    public function getFieldTypeOf(Annotation $annotation)
    {
        if ($annotation instanceof ManyToOne) {
            /** @var ManyToOne $annotation */
            $repository = $this->repositoryFactory->createRepositoryFor($annotation->targetEntity);
            $valueSupplier = new RepositoryValueSupplier($repository);
            return new ReferencingPersistenceFieldType(PersistenceFieldTypes::MANY_TO_ONE, $valueSupplier);
        } else {
            return null;
        }
    }
}