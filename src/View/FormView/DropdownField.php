<?php


namespace BasicTablePackage\View\FormView;


use BasicTablePackage\Entity\ValueSupplier;
use BasicTablePackage\View\FormView\ValueTransformers\DropdownValueTransformer;
use BasicTablePackage\View\FormView\ValueTransformers\ValueTransformer;

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
    private $value;
    /**
     * @var ValueSupplier
     */
    private $valueSupplier;

    /**
     * @param string $fieldName
     * @param ValueSupplier $valueSupplier
     * @param ValueTransformer|null $dropdownValueTransformer
     * @return \Closure
     */
    public static function createDropdownField(
        string $fieldName,
        ValueSupplier $valueSupplier,
        ValueTransformer $dropdownValueTransformer = null
    ): \Closure {
        $dropdownValueTransformer = $dropdownValueTransformer ?: new DropdownValueTransformer();
        $getValue = function ($entity, $overrideValueTransformer) use ($fieldName, $dropdownValueTransformer) {
            $persistenceValue = FormViewConfigurationFactory::extractSqlValueOfEntity($entity, $fieldName);

            $valueTransformer = $overrideValueTransformer ?: $dropdownValueTransformer;
            return $valueTransformer->transform($persistenceValue);
        };
        return function ($overrideValueTransformer) use ($fieldName, $valueSupplier, $getValue) {
            return function ($entity) use ($fieldName, $valueSupplier, $getValue, $overrideValueTransformer) {
                return new DropdownField($fieldName,
                    $fieldName,
                    $getValue($entity, $overrideValueTransformer),
                    $valueSupplier);
            };
        };
    }

    /**
     * TextField constructor.
     * @param string $label
     * @param string $postName
     * @param string $value
     * @param ValueSupplier $valueSupplier
     */
    public function __construct(string $label, string $postName, $value, ValueSupplier $valueSupplier)
    {
        $this->label = $label;
        $this->value = $value;
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
        $variables = array(
            "postname" => $this->postName,
            "sqlValue" => $this->value,
            "options"  => $this->valueSupplier->getValues(),
        );
        extract($variables);
        ob_start();
        include __DIR__ . "/../../../resources/formfields/dropdownfield.php";
        $content = ob_get_clean();
        return $content;
    }
}