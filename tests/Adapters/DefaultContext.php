<?php


namespace BaclucC5Crud\Test\Adapters;


use BaclucC5Crud\Controller\VariableSetter;

class DefaultContext implements VariableSetter
{
    /**
     * @var array
     */
    private $context;

    public function set(string $name, $value)
    {
        $this->context[$name] = $value;
    }

    public function getContext(): array
    {
        return $this->context;
    }


}