<?php


namespace BasicTablePackage;


use BasicTablePackage\View\FormView\FormView;
use BasicTablePackage\View\FormView\FormViewFieldConfiguration;

class FormViewService
{
    /**
     * @var FormViewFieldConfiguration
     */
    private $formViewFieldConfiguration;

    public function __construct (FormViewFieldConfiguration $formViewFieldConfiguration)
    {
        $this->formViewFieldConfiguration = $formViewFieldConfiguration;
    }

    public function getFormView (): FormView
    {
        $fields = [];
        foreach ($this->formViewFieldConfiguration as $sqlFieldName => $fieldFactory) {
            $fields[] = call_user_func($fieldFactory, null);
        }
        return new FormView($fields);
    }
}