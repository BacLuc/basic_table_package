<?php


namespace BasicTablePackage\Controller\ActionProcessors;


use BasicTablePackage\Controller\ActionProcessor;
use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Controller\Validation\ValidationResultItem;
use BasicTablePackage\Controller\Validation\Validator;
use BasicTablePackage\Controller\ValuePersisters\FieldPersistor;
use BasicTablePackage\Controller\ValuePersisters\PersistorConfiguration;
use BasicTablePackage\Entity\Repository;
use function BasicTablePackage\Lib\collect as collect;

class PostForm implements ActionProcessor
{
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var ShowNewEntryForm
     */
    private $showFormActionProcessor;
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var PersistorConfiguration
     */
    private $peristorConfiguration;

    /**
     * PostFormActionProcessor constructor.
     */
    public function __construct (Validator $validator,
                                 ShowNewEntryForm $showFormActionProcessor,
                                 Repository $repository,
                                 PersistorConfiguration $peristorConfiguration)
    {
        $this->validator = $validator;
        $this->showFormActionProcessor = $showFormActionProcessor;
        $this->repository = $repository;
        $this->peristorConfiguration = $peristorConfiguration;
    }

    function getName (): string
    {
        return ActionRegistryFactory::POST_FORM;
    }

    function process (array $get, array $post, ...$additionalParameters)
    {
        $validationResult = $this->validator->validate($post);
        if (!$validationResult->isError()) {
            $postValues = collect($validationResult)
                ->keyBy(function (ValidationResultItem $validationResultItem) { return $validationResultItem->getName(); })
                ->map(function (ValidationResultItem $validationResultItem) { return $validationResultItem->getPostValue(); });
            if (count($additionalParameters) == 1 && $additionalParameters[0] != null) {
                $entity = $this->repository->getById($additionalParameters[0]);
            }
            else {
                $entity = $this->repository->create();
            }
            /**
             * @var FieldPersistor $persistor
             */
            foreach ($this->peristorConfiguration as $persistor) {
                $persistor->persist($postValues, $entity);
            }
            $this->repository->persist($entity);
        }
        else {
            $this->showFormActionProcessor->process($get, $post);
        }
    }

}