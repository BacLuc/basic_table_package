<?php


namespace BasicTablePackage\Controller\Validation;


class NotNullValidator implements FieldValidator
{
    /**
     * @var string
     */
    private $name;


    /**
     * NotNullValidator constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function validate($post): ValidationResultItem
    {
        $postValue = key_exists($this->name, $post) ? $post[$this->name] : null;
        return new ValidationResultItem($this->name, $postValue, $postValue ? [] : ["This field must not be empty"]);
    }
}