<?php


namespace BaclucC5Crud\Controller\ValuePersisters;


interface FieldPersistor
{
    /**
     * @param $value
     * @param $toEntity
     * @return void
     */
    public function persist($valueMap, $toEntity);
}