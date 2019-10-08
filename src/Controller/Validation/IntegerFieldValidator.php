<?php


namespace BasicTablePackage\Controller\Validation;


class IntegerFieldValidator implements FieldValidator
{
    const NOINTERRORMSG = "This is not a valid number";

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
        if (filter_var($postValue, FILTER_VALIDATE_INT) === false && $postValue != null) {
            return new ValidationResultItem($this->name, $postValue, [self::NOINTERRORMSG]);
        }
        return new ValidationResultItem($this->name, $postValue, []);
    }
}