<?php


namespace BasicTablePackage\Controller\ActionProcessors;


interface FieldValidator
{
    public function validate ($post): ValidationResultItem;
}