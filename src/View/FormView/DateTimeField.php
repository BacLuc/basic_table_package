<?php


namespace BaclucC5Crud\View\FormView;


use BaclucC5Crud\View\FormView\ValueTransformers\DateTimeValueTransformer;
use DateTime;

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
    private $value;

    /**
     * TextField constructor.
     * @param string $label
     * @param string $postname
     * @param string $value
     */
    public function __construct(string $label, string $postname, string $value)
    {
        $this->label = $label;
        $this->value = $value;
        $this->postname = $postname;
    }

    public function getFormView(): string
    {
        $output =
            $this->value ?
                DateTime::createFromFormat(DateTimeValueTransformer::DATETIME_FORMAT, $this->value)
                : "";
        if ($output instanceof DateTime) {
            $output = $output->format("Y-m-d\TH:i");
        } else {
            $output = $this->value;
        }
        $variables = array(
            "postname"             => $this->postname,
            "datetime_local_value" => $output,
            "sqlValue"             => $this->value,
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