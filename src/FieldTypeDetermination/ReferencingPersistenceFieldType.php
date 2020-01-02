<?php


namespace BasicTablePackage\FieldTypeDetermination;


use BasicTablePackage\Entity\ValueSupplier;

class ReferencingPersistenceFieldType implements PersistenceFieldType
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    public function __construct(string $type, ValueSupplier $valueSupplier)
    {
        $this->type = $type;
        $this->valueSupplier = $valueSupplier;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return ValueSupplier
     */
    public function getValueSupplier(): ValueSupplier
    {
        return $this->valueSupplier;
    }


}