<?php

namespace BaclucC5Crud\FieldTypeDetermination;

use BaclucC5Crud\Entity\RepositoryFactory;
use BaclucC5Crud\Entity\RepositoryValueSupplier;
use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

class OneToManyAnnotationHandler implements PersistenceFieldTypeHandler {
    /**
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    public function __construct(RepositoryFactory $repositoryFactory) {
        $this->repositoryFactory = $repositoryFactory;
    }

    public function canHandle(Annotation $annotation): bool {
        return $annotation instanceof OneToMany;
    }

    public function getFieldTypeOf(Annotation $annotation) {
        if ($annotation instanceof OneToMany) {
            /** @var ManyToOne $annotation */
            $repository = $this->repositoryFactory->createRepositoryFor($annotation->targetEntity);
            $valueSupplier = new RepositoryValueSupplier($repository);

            return new ReferencingPersistenceFieldType(PersistenceFieldTypes::MANY_TO_ONE, $valueSupplier);
        }

        return null;
    }
}
