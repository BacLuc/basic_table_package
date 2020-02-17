<?php


namespace BaclucC5Crud\Entity;


interface RepositoryFactory
{
    public function createRepositoryFor(string $className): Repository;
}