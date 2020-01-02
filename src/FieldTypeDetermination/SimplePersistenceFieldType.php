<?php


namespace BasicTablePackage\FieldTypeDetermination;


class SimplePersistenceFieldType implements PersistenceFieldType
{
    /**
     * @var string
     */
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}