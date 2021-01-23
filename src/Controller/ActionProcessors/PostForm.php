<?php

namespace BaclucC5Crud\Controller\ActionProcessors;

use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\Validation\ValidationResultItem;
use BaclucC5Crud\Controller\Validation\Validator;
use BaclucC5Crud\Controller\ValuePersisters\FieldPersistor;
use BaclucC5Crud\Controller\ValuePersisters\PersistorConfiguration;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\Entity\Identifiable;
use BaclucC5Crud\Entity\Repository;
use BaclucC5Crud\FormViewAfterValidationFailedService;
use function BaclucC5Crud\Lib\collect as collect;
use BaclucC5Crud\View\CancelFormViewAction;
use BaclucC5Crud\View\FormType;
use BaclucC5Crud\View\SubmitFormViewAction;

class PostForm implements ActionProcessor {
    const FORM_VIEW = 'view/form';
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var FormViewAfterValidationFailedService
     */
    private $formViewAfterValidationFailedService;
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var PersistorConfiguration
     */
    private $peristorConfiguration;
    /**
     * @var VariableSetter
     */
    private $variableSetter;
    /**
     * @var Renderer
     */
    private $renderer;
    /**
     * @var FormType
     */
    private $formType;
    /**
     * @var SubmitFormViewAction
     */
    private $submitFormAction;
    /**
     * @var CancelFormViewAction
     */
    private $cancelFormAction;

    /**
     * PostFormActionProcessor constructor.
     */
    public function __construct(
        Validator $validator,
        FormViewAfterValidationFailedService $formViewAfterValidationFailedService,
        Repository $repository,
        PersistorConfiguration $peristorConfiguration,
        VariableSetter $variableSetter,
        Renderer $renderer,
        FormType $formType,
        SubmitFormViewAction $submitFormAction,
        CancelFormViewAction $cancelFormAction
    ) {
        $this->validator = $validator;
        $this->formViewAfterValidationFailedService = $formViewAfterValidationFailedService;
        $this->repository = $repository;
        $this->peristorConfiguration = $peristorConfiguration;
        $this->variableSetter = $variableSetter;
        $this->renderer = $renderer;
        $this->formType = $formType;
        $this->submitFormAction = $submitFormAction;
        $this->cancelFormAction = $cancelFormAction;
    }

    public function getName(): string {
        return ActionRegistryFactory::POST_FORM;
    }

    public function process(array $get, array $post, ...$additionalParameters) {
        $validationResult = $this->validator->validate($post);
        $editId = null;
        if (1 == count($additionalParameters) && null != $additionalParameters[0]) {
            $editId = $additionalParameters[0];
        }
        if (!$validationResult->isError()) {
            $postValues = collect($validationResult)
                ->keyBy(function (ValidationResultItem $validationResultItem) {
                    return $validationResultItem->getName();
                })
                ->map(function (ValidationResultItem $validationResultItem) {
                    return $validationResultItem->getPostValue();
                })
            ;
            // @var $entity Identifiable
            if (null != $editId) {
                $entity = $this->repository->getById($editId);
                if (null === $entity && $this->formType === FormType::$BLOCK_CONFIGURATION) {
                    $entity = $this->repository->create();
                }
            } else {
                $entity = $this->repository->create();
            }
            if ($this->formType === FormType::$BLOCK_CONFIGURATION) {
                $entity->setId($editId);
            }
            /**
             * @var FieldPersistor $persistor
             */
            foreach ($this->peristorConfiguration as $persistor) {
                $persistor->persist($postValues, $entity);
            }
            $this->repository->persist($entity);
        } else {
            $formView = $this->formViewAfterValidationFailedService->getFormView($validationResult);
            $this->variableSetter->set('fields', $formView->getFields());
            $this->variableSetter->set('editId', $editId);
            $validationErrors = collect($validationResult)
                ->keyBy(function (ValidationResultItem $resultItem) {
                    return $resultItem->getName();
                })->map(function (ValidationResultItem $resultItem) {
                    return $resultItem->getMessages();
                });
            $this->variableSetter->set('validationErrors', $validationErrors);
            $this->variableSetter->set('addFormTags', $this->formType === FormType::$BLOCK_VIEW);
            $this->variableSetter->set('submitFormAction', $this->submitFormAction);
            $this->variableSetter->set('cancelFormAction', $this->cancelFormAction);
            $this->renderer->render(self::FORM_VIEW);
        }
    }
}
