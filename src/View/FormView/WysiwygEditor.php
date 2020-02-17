<?php


namespace BaclucC5Crud\View\FormView;


interface WysiwygEditor
{
    public function render(string $postname, $sqlValue);
}