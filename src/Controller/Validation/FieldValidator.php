<?php


namespace BasicTablePackage\Controller\Validation;


interface FieldValidator
{
    public function validate($post): ValidationResultItem;
}