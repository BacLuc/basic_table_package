<?php


namespace BasicTablePackage\Controller\ActionProcessors;


use BasicTablePackage\Controller\ActionProcessor;
use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Entity\Repository;

class DeleteEntry implements ActionProcessor
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * PostFormActionProcessor constructor.
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    function getName(): string
    {
        return ActionRegistryFactory::DELETE_ENTRY;
    }

    function process(array $get, array $post, ...$additionalParameters)
    {
        if (count($additionalParameters) == 1 && $additionalParameters[0] != null) {
            $deleteId = $additionalParameters[0];
            $toDeleteEntity = $this->repository->getById($deleteId);
            $this->repository->delete($toDeleteEntity);
        }
    }

}