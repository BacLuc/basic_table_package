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
use BaclucC5Crud\Entity\Repository;
use BaclucC5Crud\FormViewAfterValidationFailedService;
use BaclucC5Crud\View\CancelFormViewAction;
use BaclucC5Crud\View\FormType;
use BaclucC5Crud\View\SubmitFormViewAction;
use function BaclucC5Crud\Lib\collect as collect;

class PostForm implements ActionProcessor
{
    const FORM_VIEW = "view/form";
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
     * @param Validator $validator
     * @param FormViewAfterValidationFailedService $formViewAfterValidationFailedService
     * @param Repository $repository
     * @param PersistorConfiguration $peristorConfiguration
     * @param VariableSetter $variableSetter
     * @param Renderer $renderer
     * @param FormType $formType
     * @param SubmitFormViewAction $submitFormAction
     * @param CancelFormViewAction $cancelFormAction
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

    function getName(): string
    {
        return ActionRegistryFactory::POST_FORM;
    }

    function process(array $get, array $post, ...$additionalParameters)
    {
        $validationResult = $this->validator->validate($post);
        $editId = null;
        if (count($additionalParameters) == 1 && $additionalParameters[0] != null) {
            $editId = $additionalParameters[0];
        }
        if (!$validationResult->isError()) {
            $postValues = collect($validationResult)
                ->keyBy(function (ValidationResultItem $validationResultItem) {
                    return $validationResultItem->getName();
                })
                ->map(function (ValidationResultItem $validationResultItem) {
                    return $validationResultItem->getPostValue();
                });
            if ($editId != null) {
                $entity = $this->repository->getById($editId);
                if ($entity === null && $this->formType === FormType::$BLOCK_CONFIGURATION) {
                    $entity = $this->repository->create();
                }
            } else {
                $entity = $this->repository->create();
            }
            if ($this->formType === FormType::$BLOCK_CONFIGURATION) {
                $entity->id = $editId;
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
            $this->variableSetter->set("fields", $formView->getFields());
            $this->variableSetter->set("editId", $editId);
            $validationErrors = collect($validationResult)
                ->keyBy(function (ValidationResultItem $resultItem) {
                    return $resultItem->getName();
                })->map(function (ValidationResultItem $resultItem) {
                    return $resultItem->getMessages();
                });
            $this->variableSetter->set("validationErrors", $validationErrors);
            $this->variableSetter->set("addFormTags", $this->formType === FormType::$BLOCK_VIEW);
            $this->variableSetter->set("submitFormAction", $this->submitFormAction);
            $this->variableSetter->set("cancelFormAction", $this->cancelFormAction);
            $this->renderer->render(self::FORM_VIEW);
        }
    }

}