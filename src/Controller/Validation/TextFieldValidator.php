<?php

namespace BaclucC5Crud\Controller\Validation;

class TextFieldValidator implements FieldValidator {
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
        return new ValidationResultItem($this->name, key_exists($this->name, $post) ? $post[$this->name] : null, []);
    }
}
