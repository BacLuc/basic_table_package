<?php


namespace BaclucC5Crud\Entity;


interface WithUniqueStringRepresentation
{
    public function createUniqueString(): string;
}