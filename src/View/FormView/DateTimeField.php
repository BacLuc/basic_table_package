<?php


namespace BasicTablePackage\View\FormView;


class DateTimeField implements Field
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
     * @var ?\DateTime
     */
    private $sqlValue;

    /**
     * TextField constructor.
     * @param string $label
     * @param string $postname
     * @param \DateTime|null $sqlValue
     */
    public function __construct(string $label, string $postname, ?\DateTime $sqlValue)
    {
        $this->label = $label;
        $this->sqlValue = $sqlValue;
        $this->postname = $postname;
    }

    public function getFormView(): string
    {
        $output = $this->sqlValue ? $this->sqlValue->format("Y-m-d\TH:i") : "";
        $sqlValue = $this->sqlValue ? $this->sqlValue->format("Y-m-d H:i") : "";
        $variables = array(
            "postname" => $this->postname,
            "datetime_local_value" => $output,
            "sqlValue" => $sqlValue,
        );
        extract($variables);
        ob_start();
        include __DIR__ . "/../../../resources/formfields/datetimefield.php";
        $content = ob_get_clean();
        return $content;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}