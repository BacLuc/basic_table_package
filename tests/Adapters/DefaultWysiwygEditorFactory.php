<?php


namespace BaclucC5Crud\Test\Adapters;


use BaclucC5Crud\View\FormView\WysiwygEditor;
use BaclucC5Crud\View\FormView\WysiwygEditorFactory;

class DefaultWysiwygEditorFactory implements WysiwygEditorFactory
{

    function createEditor(): WysiwygEditor
    {
        return new DefaultWysiwygEditor();
    }
}