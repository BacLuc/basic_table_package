<?php


namespace BasicTablePackage\Entity;


interface ValueSupplier
{
    function getValues(): array;
}