<?php


namespace BasicTablePackage\Entity;


interface RepositoryFactory
{
    public function createRepositoryFor(string $className): Repository;
}