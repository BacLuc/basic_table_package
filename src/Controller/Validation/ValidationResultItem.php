<?php

namespace BaclucC5Crud\Controller\Validation;

class ValidationResultItem {
    /**
     * @var string
     */
    private $name;
    private $postValue;
    /**
     * @var array
     */
    private $messages;

    /**
     * ValidationResultItem constructor.
     *
     * @param mixed $postValue
     */
    public function __construct(string $name, $postValue, array $messages) {
        $this->name = $name;
        $this->postValue = $postValue;
        $this->messages = $messages;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPostValue() {
        return $this->postValue;
    }

    /**
     * @return array
     */
    public function getMessages() {
        return $this->messages;
    }

    public function isError(): bool {
        return 0 != count($this->messages);
    }
}
