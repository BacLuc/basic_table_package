<?php


namespace BaclucC5Crud\Adapters\Concrete5;


use BaclucC5Crud\View\FormView\WysiwygEditor;
use Concrete\Core\Editor\EditorInterface;
use Concrete\Core\Editor\LinkAbstractor;

class Concrete5WysiwygEditor implements WysiwygEditor
{
    /**
     * @var EditorInterface
     */
    private $editor;

    public function __construct(EditorInterface $editor)
    {
        $this->editor = $editor;
    }

    public function render(string $postname, $sqlValue)
    {
        return $this->editor->outputStandardEditor($postname, LinkAbstractor::translateFromEditMode($sqlValue));
    }
}