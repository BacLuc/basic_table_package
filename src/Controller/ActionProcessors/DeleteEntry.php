<?php

namespace BaclucC5Crud\Controller\ActionProcessors;

use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Entity\Repository;

class DeleteEntry implements ActionProcessor {
    /**
     * @var Repository
     */
    private $repository;

    /**
     * PostFormActionProcessor constructor.
     */
    public function __construct(Repository $repository) {
        $this->repository = $repository;
    }

    public function getName(): string {
        return ActionRegistryFactory::DELETE_ENTRY;
    }

    public function process(array $get, array $post, ...$additionalParameters) {
        if (1 == count($additionalParameters) && null != $additionalParameters[0]) {
            $deleteId = $additionalParameters[0];
            $toDeleteEntity = $this->repository->getById($deleteId);
            $this->repository->delete($toDeleteEntity);
        }
    }
}
