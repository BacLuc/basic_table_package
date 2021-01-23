<?php

namespace BaclucC5Crud\Controller\ValuePersisters;

use BaclucC5Crud\Entity\ValueSupplier;
use function BaclucC5Crud\Lib\collect as collect;
use Doctrine\Common\Collections\ArrayCollection;

class ManyToManyFieldPersistor implements FieldPersistor {
    /**
     * @var string
     */
    private $name;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    /**
     * TextFieldPersistor constructor.
     */
    public function __construct(string $name, ValueSupplier $valueSupplier) {
        $this->name = $name;
        $this->valueSupplier = $valueSupplier;
    }

    public function persist($valueMap, $toEntity) {
        $values = $this->valueSupplier->getValues();
        $postvalues = $valueMap[$this->name];
        if (false !== filter_var($postvalues, FILTER_VALIDATE_INT) || is_string($postvalues)) {
            $postvalues = [$postvalues];
        }

        $newCollection = collect($postvalues)
            ->map(function ($postValue) use ($values) {
                return $values[$postValue];
            })
            ->reduce(
                function (ArrayCollection $collection, $item) {
                    $collection->add($item);

                    return $collection;
                },
                new ArrayCollection()
            )
        ;

        $toEntity->{$this->name} = $newCollection;
    }
}
