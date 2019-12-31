<?php


namespace BasicTablePackage\View\FormView;


class WysiwygField implements Field
{

    /**
     * @var WysiwygEditor
     */
    private $wysiwygEditor;
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
     * @param WysiwygEditor $wysiwygEditor
     * @param string $label
     * @param string $postname
     * @param string $sqlValue
     */
    public function __construct(WysiwygEditor $wysiwygEditor, string $label, string $postname, $sqlValue)
    {
        $this->wysiwygEditor = $wysiwygEditor;
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
        return $this->wysiwygEditor->render($this->postname, $this->sqlValue);
    }
}