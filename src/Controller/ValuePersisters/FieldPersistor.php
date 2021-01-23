<?php

namespace BaclucC5Crud\Controller\ValuePersisters;

interface FieldPersistor {
    /**
     * @param $value
     * @param $toEntity
     * @param mixed $valueMap
     */
    public function persist($valueMap, $toEntity);
}
