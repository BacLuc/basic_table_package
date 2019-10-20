<?php


namespace BasicTablePackage\View\FormView;


interface Field
{
    public function getFormView(): string;

    public function getLabel(): string;
}