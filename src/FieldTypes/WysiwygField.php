<?php

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;


use Concrete\Core\Editor\RedactorEditor;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\Field as Field;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\HelperbBlock\Content\Application\Block\BasicTableBlock\FieldTypes\HelperbBlock\Content;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\HelperBlocks\Content\Controller as ContentController;

//use Application\Block\BasicTableBlock\FieldTypes\HelperbBlock\Content\Test as Test;

/**
 * Class WysiwygField
 * @IgnoreAnnotation("package")\n*  Concrete\Package\BasicTablePackage\Src\FieldTypes
 * A Wysiwyg field, uses Helperclass which extends the ContentController of the Concrete Core,
 * so that he doesn't store the content in the Database himself,
 * but provides a method for this fieldtype to get the content and provide it for the blockcontroller to save it in the model
 */
class WysiwygField extends Field
{
    /**
     *
     * @var ContentController
     */
    protected $blockController;

    public function __construct($sqlFieldname, $label, $postName, $showInTable = false, $showInForm = true)
    {

        $this->sqlFieldname = $sqlFieldname;
        $this->label = $label;
        $this->postName = $postName;
        $this->showInTable = $showInTable;
        $this->showInForm = $showInForm;
        $this->blockController = new ContentController();

    }


    public function setSQLValue($value)
    {
        $this->value = $value;
        $this->blockController->setValue($value);
        return $this;
    }

    public function getTableView()
    {
        return $this->blockController->getContent();
    }

    public function getFormView($form, $clientSideValidationActivated = true)
    {


        $html = '';
        //$html = "<label for='".$this->getPostName()."'>".$this->getPostName()."</label>";
        $html .= $form->label($this->getHtmlId(), t($this->getLabel()));


        $html .= $this->getInputHtml($form, $clientSideValidationActivated);
        return $html;
    }

    /**
     * @param $html
     * @return string
     */
    public function getInputHtml($form, $clientSideValidationActivated = true)
    {
        $editor = new RedactorEditor();
        $value = $this->getSQLValue();
        $default = $this->getDefault();
        if ($value == null && $default != null) {
            $value = $default;
        }
        $html = $editor->outputStandardEditor($this->getPostName(), $value);

        $html .= $this->getHtmlErrorMsg();
        return $html;
    }

    public function getSQLValue()
    {
        return $this->value;
    }

}
