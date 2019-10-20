<?php


namespace BasicTablePackage\Test\Adapters;


use BasicTablePackage\Controller\VariableSetter;

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