<?php

namespace BaclucC5Crud\View\FormView;

class DateField implements Field {
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
     */
    public function __construct(string $label, string $postname, string $value) {
        $this->label = $label;
        $this->value = $value;
        $this->postname = $postname;
    }

    public function getFormView(): string {
        $variables = [
            'postname' => $this->postname,
            'sqlValue' => $this->value,
        ];
        extract($variables);
        ob_start();

        include __DIR__.'/../../../resources/formfields/datefield.php';
        $content = ob_get_clean();

        return $content;
    }

    public function getLabel(): string {
        return $this->label;
    }
}
