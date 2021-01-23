<?php

namespace BaclucC5Crud\Controller\Validation;

class IntegerFieldValidator implements FieldValidator {
    const NOINTERRORMSG = 'This is not a valid number';

    /**
     * @var string
     */
    private $name;

    /**
     * TextFieldValidator constructor.
     */
    public function __construct(string $name) {
        $this->name = $name;
    }

    public function validate($post): ValidationResultItem {
        $postValue = key_exists($this->name, $post) ? $post[$this->name] : null;
        if (false === filter_var($postValue, FILTER_VALIDATE_INT) && null != $postValue) {
            return new ValidationResultItem($this->name, $postValue, [self::NOINTERRORMSG]);
        }

        return new ValidationResultItem($this->name, $postValue, []);
    }
}
