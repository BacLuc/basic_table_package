<?php


namespace BasicTablePackage;


use BasicTablePackage\View\FormView\FormView;
use BasicTablePackage\View\FormView\FormViewFieldConfiguration;
use function BasicTablePackage\Lib\collect as collect;

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
        $fields =
            collect($this->formViewFieldConfiguration)->map(function ($fieldFactory) {
                return call_user_func($fieldFactory, null);
            });
        return new FormView($fields->toArray());
    }
}