<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.04.17
 * Time: 14:25
 */

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;


use Concrete\Core\Support\Facade\Application;
use Concrete\Package\BasicTablePackage\Src\Exceptions\NotImplementedException;

abstract class AbstractDirectEditField extends DropdownLinkField implements DirectEditInterface
{

    const SUBFORMERROR = " has an Error.";

    protected $subErrorMsg = array();

    protected $alwaysCreateNewInstance = false;

    /**
     * @return boolean
     */
    public function isAlwaysCreateNewInstance()
    {
        return $this->alwaysCreateNewInstance;
    }

    /**
     * @param boolean $alwaysCreateNewInstance
     * @return $this
     */
    public function setAlwaysCreateNewInstance($alwaysCreateNewInstance)
    {
        $this->alwaysCreateNewInstance = $alwaysCreateNewInstance;
        return $this;
    }

    public function getFormView($form, $clientSideValidationActivated = true)
    {
        $html = "
        <div class='subentityedit col-xs-12'>
            <label>" . $this->getLabel() . "</label>
            <div class='row'>
        ";
        $html .= $this->getInputHtml($form, $clientSideValidationActivated);
        $html .= "</div>
           </div>
        ";
        return $html;
    }

    function getInputHtml($form, $clientSideValidationActivated = true)
    {
        throw new NotImplementedException(sprintf("To extend %s, you need to override %s in the child class", __CLASS__,
            __METHOD__));
    }

    protected function saveSubErrorMsg()
    {
        $app = Application::getFacadeApplication();

        /**
         * @var Session $session
         */
        $session = $app['session'];

        $session->set($this->postName . "subformerrors", $this->subErrorMsg);
    }

    protected function loadSubErrorMsg()
    {
        $app = Application::getFacadeApplication();
        /**
         * @var Session $session
         */
        $session = $app['session'];
        if (count($this->subErrorMsg) > 0) {
            $session->remove($this->postName . "subformerrors");
            return $this->subErrorMsg;
        }

        $this->subErrorMsg = $session->get($this->postName . "subformerrors", array());
        $session->remove($this->postName . "subformerrors");
        return $this->subErrorMsg;

    }


}