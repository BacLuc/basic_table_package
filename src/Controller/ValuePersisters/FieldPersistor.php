<?php


namespace BasicTablePackage\Controller\ValuePersisters;


interface FieldPersistor
{
    /**
     * @param $value
     * @param $toEntity
     * @return void
     */
    public function persist($valueMap, $toEntity);
}