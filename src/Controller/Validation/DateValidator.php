<?php

namespace BaclucC5Crud\Controller\Validation;

class DateValidator implements FieldValidator {
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
        if (null === $postValue || '' === $postValue
            || (false !== strtotime($postValue)
                && false === filter_var(
                    $postValue,
                    FILTER_VALIDATE_FLOAT
                ))) {
            return new ValidationResultItem($this->name, $postValue, []);
        }

        return new ValidationResultItem($this->name, $postValue, ['The value must be in a valid Date format']);
    }
}
