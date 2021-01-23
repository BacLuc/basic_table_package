<?php

namespace BaclucC5Crud\Controller\Validation;

use BaclucC5Crud\Entity\ValueSupplier;
use function BaclucC5Crud\Lib\collect as collect;

class SelectMultipleFieldValidator implements FieldValidator {
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

    public function validate($post): ValidationResultItem {
        $postValue = key_exists($this->name, $post) ? $post[$this->name] : null;
        if ('' === $postValue || null === $postValue) {
            $postValue = [];
        }
        if (!is_array($postValue)) {
            return new ValidationResultItem($this->name, $postValue, ['the value must be an array']);
        }
        $possibleValues = $this->valueSupplier->getValues();
        $areAllValuesValid = 0 == collect($postValue)
            ->filter(function ($value) use ($possibleValues) {
                return !isset($possibleValues[$value]);
            })->count();
        if ([] !== $postValue && !$areAllValuesValid) {
            return new ValidationResultItem(
                $this->name,
                $postValue,
                [
                    'One of the values sent ['.
                    implode(', ', $postValue).
                    '] is not in the range of possible values. Possible are: ['.
                    implode(',', $possibleValues).
                    ']',
                ]
            );
        }

        return new ValidationResultItem($this->name, $postValue, []);
    }
}
