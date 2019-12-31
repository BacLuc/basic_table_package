<?php


namespace BasicTablePackage\View\FormView;


interface WysiwygEditor
{
    public function render(string $postname, $sqlValue);
}