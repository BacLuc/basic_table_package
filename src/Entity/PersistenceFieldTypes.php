<?php


namespace BasicTablePackage\Entity;


use ReflectionClass;

class PersistenceFieldTypes
{
    const        INTEGER  = "integer";
    const        STRING   = "string";
    const        DATE     = "date";
    const        DATETIME = "datetime";
    public const TEXT     = "text";

    /**
     * @throws \ReflectionException
     */
    public static function getTypes()
    {
        $reflectionClass = new ReflectionClass(new self());
        return $reflectionClass->getConstants();
    }
}