<?php


namespace BaclucC5Crud\Controller\Validation;

use function BaclucC5Crud\Lib\collect as collect;

class CombinedValidator implements FieldValidator
{
    /**
     * @var string
     */
    private $name;
    private $validators;


    /**
     * CombinedValidator constructor.
     * @param string $name
     * @param FieldValidator[] $validators
     */
    public function __construct(string $name, array $validators)
    {
        $nonFieldValidators = collect($validators)->filter(function ($validator) {
            return !$validator instanceof FieldValidator;
        })->count();
        if ($nonFieldValidators > 0) {
            throw new \InvalidArgumentException("the validators passed must be of type FieldValidator");
        }
        $this->validators = $validators;
        $this->name = $name;
    }

    public function validate($post): ValidationResultItem
    {
        $postValue = key_exists($this->name, $post) ? $post[$this->name] : null;
        $combinedMessages = collect($this->validators)->map(function (FieldValidator $validator) use ($post) {
            return $validator->validate($post);
        })->map(function (ValidationResultItem $validationResultItem) {
            return $validationResultItem->getMessages();
        })->reduce(function ($array, $newMessages) {
            foreach ($newMessages as $newMessage) {
                $array[] = $newMessage;
            }
            return $array;
        },
            []);
        return new ValidationResultItem($this->name, $postValue, $combinedMessages);
    }
}