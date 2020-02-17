<?php


namespace BaclucC5Crud\View\FormView;


class IntegerField implements Field
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

    public function getFormView(): string
    {
        $variables = array(
            "postname" => $this->postname,
            "sqlValue" => $this->sqlValue,
        );
        extract($variables);
        ob_start();
        include __DIR__ . "/../../../resources/formfields/integerfield.php";
        $content = ob_get_clean();
        return $content;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}