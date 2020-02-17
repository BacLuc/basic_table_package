<?php


namespace BaclucC5Crud\Controller\ActionProcessors;


use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\FormViewService;
use BaclucC5Crud\View\FormType;

class ShowNewEntryForm implements ActionProcessor
{
    const FORM_VIEW = "view/form";
    /**
     * @var FormViewService
     */
    private $formViewService;
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
     * ShowFormActionProcessor constructor.
     * @param FormViewService $formViewService
     * @param VariableSetter $variableSetter
     * @param Renderer $renderer
     * @param FormType $formType
     */
    public function __construct(
        FormViewService $formViewService,
        VariableSetter $variableSetter,
        Renderer $renderer,
        FormType $formType
    ) {
        $this->formViewService = $formViewService;
        $this->variableSetter = $variableSetter;
        $this->renderer = $renderer;
        $this->formType = $formType;
    }


    function getName(): string
    {
        return ActionRegistryFactory::ADD_NEW_ROW_FORM;
    }

    function process(array $get, array $post, ...$additionalParameters)
    {
        $formView = $this->formViewService->getFormView();
        $this->variableSetter->set("fields", $formView->getFields());
        $this->variableSetter->set("addFormTags", $this->formType === FormType::$BLOCK_VIEW);
        $this->renderer->render(self::FORM_VIEW);
    }

}