<?php

namespace BaclucC5Crud\Entity;

interface Identifiable {
    public function getId();

    public function setId(int $id);

    public static function getIdFieldName(): string;
}
