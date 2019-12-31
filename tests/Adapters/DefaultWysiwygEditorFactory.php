<?php


namespace BasicTablePackage\Test\Adapters;


use BasicTablePackage\View\FormView\WysiwygEditor;
use BasicTablePackage\View\FormView\WysiwygEditorFactory;

class DefaultWysiwygEditorFactory implements WysiwygEditorFactory
{

    function createEditor(): WysiwygEditor
    {
        return new DefaultWysiwygEditor();
    }
}