<?php


namespace BasicTablePackage\View\FormView;


use BasicTablePackage\Entity\ValueSupplier;

class Dropdownfield implements Field
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
     * @var string
     */
    private $sqlValue;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    /**
     * @param string $fieldname
     * @param ValueSupplier $valueSupplier
     * @return \Closure
     */
    public static function createDropdownField(string $fieldname, ValueSupplier $valueSupplier): \Closure
    {
        return function ($entity) use ($fieldname, $valueSupplier) {
            return new Dropdownfield($fieldname,
                $fieldname,
                FormViewConfigurationFactory::extractSqlValueOfEntity($entity, $fieldname),
                $valueSupplier);
        };
    }

    /**
     * TextField constructor.
     * @param string $label
     * @param string $postname
     * @param string $sqlValue
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

        $sqlValue = $this->sqlValue;
        if (is_object($sqlValue)) {
            $sqlValue = $sqlValue->id;
        }
        $variables = array(
            "postname" => $this->postname,
            "sqlValue" => $sqlValue,
            "options"  => $this->valueSupplier->getValues(),
        );
        extract($variables);
        ob_start();
        include __DIR__ . "/../../../resources/formfields/dropdownfield.php";
        $content = ob_get_clean();
        return $content;
    }
}