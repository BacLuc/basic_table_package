<?php


namespace BasicTablePackage\View\FormView;


class FormView
{
    /**
     * @var Field[]
     */
    private $fields;

    /**
     * FormView constructor.
     */
    public function __construct (array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return Field[]
     */
    public function getFields ()
    {
        return $this->fields;
    }
}