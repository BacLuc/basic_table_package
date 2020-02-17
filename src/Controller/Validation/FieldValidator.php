<?php


namespace BaclucC5Crud\Controller\Validation;


interface FieldValidator
{
    public function validate($post): ValidationResultItem;
}