<?php


namespace BasicTablePackage\Controller\Validation;


class DateValidator implements FieldValidator
{

    /**
     * @var string
     */
    private $name;

    /**
     * TextFieldValidator constructor.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function validate($post): ValidationResultItem
    {
        $postValue = key_exists($this->name, $post) ? $post[$this->name] : null;
        if ($postValue === null || $postValue === ""
            || (strtotime($postValue) !== false
                && filter_var($postValue,
                    FILTER_VALIDATE_FLOAT) === false)) {
            return new ValidationResultItem($this->name, $postValue, []);
        } else {
            return new ValidationResultItem($this->name, $postValue, ["The value must be in a valid Date format"]);
        }
    }
}