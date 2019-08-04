<?php


namespace BasicTablePackage\View\FormView;


class TextField implements Field
{
    /**
     * @var string
     */
    private $label;
    /**
     * @var string
     */
    private $sqlValue;

    /**
     * TextField constructor.
     * @param string $sqlValue
     */
    public function __construct (string $label, $sqlValue)
    {
        $this->label = $label;
        $this->sqlValue = $sqlValue;
    }

    /**
     * @return string
     */
    public function getLabel (): string
    {
        return $this->label;
    }


    public function getFormView (): string
    {
        $variables = array(
            "sqlValue" => $this->sqlValue,
        );
        extract($variables);
        ob_start();
        include __DIR__ . "/../../../resources/formfields/textfield.php";
        $content = ob_get_clean();
        return $content;
    }

}