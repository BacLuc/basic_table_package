<?php


namespace BaclucC5Crud\FieldTypeDetermination;


use ReflectionClass;

class PersistenceFieldTypes
{
    const        INTEGER      = "integer";
    const        STRING       = "string";
    const        DATE         = "date";
    const        DATETIME     = "datetime";
    const        TEXT         = "text";
    const        MANY_TO_ONE  = "manyToOne";
    const        MANY_TO_MANY = "manyToMany";

    /**
     * @throws \ReflectionException
     */
    public static function getTypes()
    {
        $reflectionClass = new ReflectionClass(new self());
        return $reflectionClass->getConstants();
    }
}