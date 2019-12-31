<?php


namespace BasicTablePackage\Test\Adapters;


use BasicTablePackage\View\FormView\TextField;
use BasicTablePackage\View\FormView\WysiwygEditor;

class DefaultWysiwygEditor implements WysiwygEditor
{

    public function render(string $postname, $sqlValue)
    {
        $textField = new TextField("", $postname, $sqlValue);
        return $textField->getFormView();
    }
}