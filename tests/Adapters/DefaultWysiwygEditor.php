<?php


namespace BaclucC5Crud\Test\Adapters;


use BaclucC5Crud\View\FormView\TextField;
use BaclucC5Crud\View\FormView\WysiwygEditor;

class DefaultWysiwygEditor implements WysiwygEditor
{

    public function render(string $postname, $sqlValue)
    {
        $textField = new TextField("", $postname, $sqlValue);
        return $textField->getFormView();
    }
}