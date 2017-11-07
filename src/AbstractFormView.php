<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.10.16
 * Time: 07:44
 */

namespace Concrete\Package\BasicTablePackage\Src;


use Concrete\Package\BasicTablePackage\Src\FieldTypes\DirectEditAssociatedEntityMultipleField;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\Field;

/**
 * Class AbstractFormView
 * package Concrete\Package\BasicTablePackage\Src
 * Abstract class to provide common functionality for special form views for entites
 */
abstract class AbstractFormView
{
    protected $entity;
    protected $form;
    protected $parentpostname                = null;
    protected $errorMsg                      = null;
    protected $clientSideValidationActivated = true;

    public function __construct(BaseEntity $entity, $clientSideValidationActivated = true)
    {
        $this->entity = $entity;
        $this->clientSideValidationActivated = $clientSideValidationActivated;
    }

    public function setParentPostName($postname)
    {
        $this->parentpostname = $postname;
        return $this;
    }

    public function setErrorMsg($errorMsg)
    {
        $this->errorMsg = $errorMsg;
        return $this;
    }

    public function getFilledVariables($clientSideValidationActivated = true)
    {
        $fields = $this->entity->getFieldTypes();
        $variables = array();
        foreach ($fields as $field) {
            /**
             * @var Field $field
             */
            //if id or another directedit possibility, skip (because of possible circle)
            if (($field instanceof DirectEditInterface && strlen($this->parentpostname) > 0) || !$field->showInForm()) {
                continue;
            }

            if (is_null($this->entity)) {
                $setValue = null;
            } else {
                $setValue = $this->entity->get($field->getSQLFieldName());
            }
            //set the value
            $field->setSQLValue($setValue);

            if (isset($this->errorMsg[$field->getPostName()])) {
                $field->setErrorMessage($this->errorMsg[$field->getPostName()]);
            }
            if (strlen($this->parentpostname) > 0) {
                if ($this->parentpostname == DirectEditAssociatedEntityMultipleField::PREPEND_BEFORE_REALNAME) {
                    $field->setPostName($this->parentpostname . "" . $field->getPostName() . "");
                } else {
                    $field->setPostName($this->parentpostname . "[" . $field->getPostName() . "]");
                }
                //change the post name

            }


            //get the form view
            $variables[$field->getSQLFieldName()]['input'] =
                $field->getInputHtml($this->form, $clientSideValidationActivated);
            $variables[$field->getSQLFieldName()]['label'] = $field->getLabel();
            $field->setErrorMessage(null);
        }

        return $variables;

    }

    abstract public function getFormView($form, $clientSideValidationActivated = true);
}