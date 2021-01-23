<?php

namespace BaclucC5Crud\Controller\Validation;

use BaclucC5Crud\Entity\ValueSupplier;

class DropdownFieldValidator implements FieldValidator {
    /**
     * @var string
     */
    private $name;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    /**
     * TextFieldValidator constructor.
     */
    public function __construct(string $name, ValueSupplier $valueSupplier) {
        $this->name = $name;
        $this->valueSupplier = $valueSupplier;
    }

    public static function createDropdownFieldValidator($valueSupplier) {
        return function ($key) use ($valueSupplier) {
            return new DropdownFieldValidator($key, $valueSupplier);
        };
    }

    public function validate($post): ValidationResultItem {
        $postValue = key_exists($this->name, $post) ? $post[$this->name] : null;
        if (false === filter_var($postValue, FILTER_VALIDATE_INT) && !is_string($postValue) && null !== $postValue) {
            return new ValidationResultItem($this->name, $postValue, ['the value must be an integer or string']);
        }
        if ('' === $postValue) {
            $postValue = null;
        }
        $possibleValues = $this->valueSupplier->getValues();
        if (null !== $postValue && !isset($possibleValues[$postValue])) {
            return new ValidationResultItem(
                $this->name,
                $postValue,
                [
                    "The value sent [{$postValue}] is not in the range of possible values. Possible are: [".
                    implode(',', $possibleValues).
                    ']',
                ]
            );
        }

        return new ValidationResultItem($this->name, $postValue, []);
    }
}
