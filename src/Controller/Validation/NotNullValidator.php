<?php

namespace BaclucC5Crud\Controller\Validation;

class NotNullValidator implements FieldValidator {
    /**
     * @var string
     */
    private $name;

    /**
     * NotNullValidator constructor.
     */
    public function __construct(string $name) {
        $this->name = $name;
    }

    public function validate($post): ValidationResultItem {
        $postValue = key_exists($this->name, $post) ? $post[$this->name] : null;

        return new ValidationResultItem($this->name, $postValue, $postValue ? [] : ['This field must not be empty']);
    }
}
