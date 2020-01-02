<?php


namespace BasicTablePackage\Entity;


interface WithUniqueStringRepresentation
{
    public function createUniqueString(): string;
}