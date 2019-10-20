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
    private $postname;
    /**
     * @var string
     */
    private $sqlValue;

    /**
     * TextField constructor.
     * @param string $label
     * @param string $postname
     * @param string $sqlValue
     */
    public function __construct(string $label, string $postname, $sqlValue)
    {
        $this->label = $label;
        $this->sqlValue = $sqlValue;
        $this->postname = $postname;
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
            "sqlValue" => $this->sqlValue,
        );
        extract($variables);
        ob_start();
        include __DIR__ . "/../../../resources/formfields/textfield.php";
        $content = ob_get_clean();
        return $content;
    }

}