<?php


namespace BaclucC5Crud\View\FormView;


use BaclucC5Crud\Entity\ValueSupplier;
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
    private $value;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    /**
     * TextField constructor.
     * @param string $label
     * @param string $postname
     * @param ArrayCollection $value
     * @param ValueSupplier $valueSupplier
     */
    public function __construct(string $label, string $postname, $value, ValueSupplier $valueSupplier)
    {
        $this->label = $label;
        $this->value = $value;
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

        $variables = array(
            "postname" => $this->postname,
            "sqlValue" => $this->value,
            "options"  => $this->valueSupplier->getValues(),
        );
        extract($variables);
        ob_start();
        include __DIR__ . "/../../../resources/formfields/multiselectfield.php";
        $content = ob_get_clean();
        return $content;
    }
}