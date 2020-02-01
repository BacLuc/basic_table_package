<?php


namespace BasicTablePackage\FieldTypeDetermination;


class SimplePersistenceFieldType implements PersistenceFieldType
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var bool
     */
    private $nullable;

    public function __construct(string $type, bool $nullable)
    {
        $this->type = $type;
        $this->nullable = $nullable;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        return $this->nullable;
    }


}