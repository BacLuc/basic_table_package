<?php


namespace BasicTablePackage\View\FormView;


use BasicTablePackage\Lib\IteratorTrait;
use Iterator;

class FormViewFieldConfiguration implements Iterator
{
    use IteratorTrait;

    /**
     * FormViewFieldConfiguration constructor.
     * @param array $configuration as map of $sqlFieldName => callable ($value => Field)
     */
    public function __construct(array $configuration)
    {
        $this->initialize($configuration);
    }
}