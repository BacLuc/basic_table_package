<?php


namespace BasicTablePackage\Adapters\Concrete5;


use BasicTablePackage\View\FormView\WysiwygEditor;
use BasicTablePackage\View\FormView\WysiwygEditorFactory;
use Concrete\Core\Editor\EditorInterface;

class Concrete5WysiwygEditorFactory implements WysiwygEditorFactory
{

    function createEditor(): WysiwygEditor
    {
        return new Concrete5WysiwygEditor(\Core::make(EditorInterface::class));
    }
}