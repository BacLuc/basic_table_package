<?php


namespace BaclucC5Crud\Controller\ValuePersisters;


class TextFieldPersistor implements FieldPersistor
{
    /**
     * @var string
     */
    private $name;

    /**
     * TextFieldPersistor constructor.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function persist($valueMap, $toEntity)
    {
        $toEntity->{$this->name} = $valueMap[$this->name];
    }

}