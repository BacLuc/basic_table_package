<?php

namespace BaclucC5Crud\View\FormView;

use BaclucC5Crud\Entity\ValueSupplier;
use BaclucC5Crud\Entity\WithUniqueStringRepresentation;
use function BaclucC5Crud\Lib\collect as collect;
use Doctrine\Common\Collections\ArrayCollection;

class MultiSelectField implements Field {
    /**
     * @var string
     */
    private $label;
    /**
     * @var string
     */
    private $postname;
    /**
     * @var ArrayCollection
     */
    private $value;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    /**
     * TextField constructor.
     *
     * @param ArrayCollection $value
     */
    public function __construct(string $label, string $postname, $value, ValueSupplier $valueSupplier) {
        $this->label = $label;
        $this->value = $value;
        $this->postname = $postname;
        $this->valueSupplier = $valueSupplier;
    }

    public function getLabel(): string {
        return $this->label;
    }

    public function getFormView(): string {
        $values = $this->valueSupplier->getValues();
        $values = collect($values)
            ->map(function (WithUniqueStringRepresentation $value) {
                return $value->createUniqueString();
            })
        ;
        $variables = [
            'postname' => $this->postname,
            'sqlValue' => $this->value,
            'options' => $values,
        ];
        extract($variables);
        ob_start();

        include __DIR__.'/../../../resources/formfields/multiselectfield.php';
        $content = ob_get_clean();

        return $content;
    }
}
