<?php


namespace BasicTablePackage\View\FormView;


use BasicTablePackage\Entity\ValueSupplier;

class DropdownField implements Field
{
    /**
     * @var string
     */
    private $label;
    /**
     * @var string
     */
    private $postName;
    /**
     * @var string
     */
    private $sqlValue;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    /**
     * @param string $fieldName
     * @param ValueSupplier $valueSupplier
     * @return \Closure
     */
    public static function createDropdownField(string $fieldName, ValueSupplier $valueSupplier): \Closure
    {
        return function ($entity) use ($fieldName, $valueSupplier) {
            return new DropdownField($fieldName,
                $fieldName,
                FormViewConfigurationFactory::extractSqlValueOfEntity($entity, $fieldName),
                $valueSupplier);
        };
    }

    /**
     * TextField constructor.
     * @param string $label
     * @param string $postName
     * @param string $sqlValue
     * @param ValueSupplier $valueSupplier
     */
    public function __construct(string $label, string $postName, $sqlValue, ValueSupplier $valueSupplier)
    {
        $this->label = $label;
        $this->sqlValue = $sqlValue;
        $this->postName = $postName;
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
            "postname" => $this->postName,
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