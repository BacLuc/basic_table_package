<?php

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Block\View\BlockView as View;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\Field as Field;
use File;
use Loader;
use Page;

class FileField extends Field
{

    public function __construct($sqlFieldname, $label, $postName)
    {
        parent::__construct($sqlFieldname, $label, $postName);

    }


    public function getTableView()
    {
        $f = $this->getFileObject();
        //$fp = new Permissions($f);
        if (/*$fp->canViewFile()*/
        true
        ) {
            $c = Page::getCurrentPage();
            if ($c instanceof Page) {
                $cID = $c->getCollectionID();
            }

            $returnString =
                "<a href=\"" . View::url('/download_file', $this->getSQLValue(), $cID) . "\" target='_blank'>"
                . stripslashes($this->getLinkText()) . "</a>";

        } else {
            $returnString = t("permission denied");
        }

        $returnString .= $this->getHtmlErrorMsg();
        return $returnString;
    }

    function getFileObject()
    {
        if (is_null($this->value)) {
            return false;
        }
        return File::getByID($this->value);
    }

    function getLinkText()
    {
        $f = $this->getFileObject();
        if (is_object($f)) {
            return $f->getTitle();
        } else {
            return t("no File");
        }

    }

    public function getFormView($form, $clientSideValidationActivated = true)
    {


        $returnString = "
		<div class=\"form-group\">
		" . $form->label($this->getHtmlId(), t($this->getLabel()));
        $returnString .= $this->getInputHtml($form, $clientSideValidationActivated);


        return $returnString;
    }

    /**
     * @param $returnString
     * @return string
     */
    public function getInputHtml($form, $clientSideValidationActivated = true)
    {
        /**
         * @var FileManager $al
         */
        $al = Loader::helper('concrete/asset_library');
        $bf = null;
        $value = $this->getSQLValue();
        $default = $this->getDefault();
        if ($value == null && $default != null) {
            $this->setSQLValue($default);
        }

        if ($this->getSQLValue() > 0) {
            $bf = $this->getFileObject();
        }
        $this->setSQLValue($value);
        $c = Page::getCurrentPage();
        $returnString =
            $al->file($this->getHtmlId(), $this->getPostName(), t('Choose File'), $bf) . "
		</div>";
        $valt = Loader::helper('validation/token');
        $token = $valt->generate();
        //$form->addHeaderItem('<script type="text/javascript">var CCM_SECURITY_TOKEN = \''.$token.'\';</script>');
        $returnString .= '<script type="text/javascript">var CCM_SECURITY_TOKEN = \'' . $token . '\';</script>';
        $returnString .= $this->getHtmlErrorMsg();
        return $returnString;
    }

    public function validatePost($value)
    {
        $this->value = $value;
        return true;
    }
}
