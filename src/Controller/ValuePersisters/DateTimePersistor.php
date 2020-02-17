<?php


namespace BaclucC5Crud\Controller\ValuePersisters;


class DateTimePersistor implements FieldPersistor
{
    /**
     * @var string
     */
    private $name;

    /**
     * TextFieldPersistor constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function persist($valueMap, $toEntity)
    {
        $value = $valueMap[$this->name];
        if ($value != null) {
            $dateTime = (new \DateTime())->setTimestamp(strtotime($value));
            $toEntity->{$this->name} = $dateTime;
        } else {
            $toEntity->{$this->name} = null;
        }
    }
}