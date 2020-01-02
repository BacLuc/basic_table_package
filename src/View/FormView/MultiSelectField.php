<?php


namespace BasicTablePackage\View\FormView;


use BasicTablePackage\Entity\ValueSupplier;
use Doctrine\Common\Collections\ArrayCollection;

class MultiSelectField implements Field
{
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
    private $sqlValue;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    /**
     * TextField constructor.
     * @param string $label
     * @param string $postname
     * @param ArrayCollection $sqlValue
     * @param ValueSupplier $valueSupplier
     */
    public function __construct(string $label, string $postname, $sqlValue, ValueSupplier $valueSupplier)
    {
        $this->label = $label;
        $this->sqlValue = $sqlValue;
        $this->postname = $postname;
        $this->valueSupplier = $valueSupplier;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }


    public function getFormView(): string
    {

        $arrayCollection = $this->sqlValue ?: new ArrayCollection();
        $sqlValue = collect($arrayCollection->toArray())->keyBy(function ($value) {
            return $value->id;
        })->toArray();
        $variables = array(
            "postname" => $this->postname,
            "sqlValue" => $sqlValue,
            "options"  => $this->valueSupplier->getValues(),
        );
        extract($variables);
        ob_start();
        include __DIR__ . "/../../../resources/formfields/multiselectfield.php";
        $content = ob_get_clean();
        return $content;
    }
}