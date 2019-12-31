<?php


namespace BasicTablePackage\View\FormView;


interface WysiwygEditorFactory
{
    function createEditor(): WysiwygEditor;
}