<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 22.08.16
 * Time: 18:59
 */

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;


use Concrete\Package\BasicTablePackage\Src\AbstractFormView;
use Concrete\Package\BasicTablePackage\Src\BaseEntity;
use Concrete\Package\BasicTablePackage\Src\BaseEntityRepository;
use Doctrine\ORM\Proxy\Proxy;

class DirectEditAssociatedEntityField extends AbstractDirectEditField
{


    public function validatePost($value)
    {
        if ($value == null || !is_array($value)) {
            $this->setSQLValue(null);
        }

        /**
         * @var BaseEntity $instanceforidfield ;
         */
        $instanceforidfield = new $this->targetEntity();
        $idfieldname = $instanceforidfield->getIdFieldName();

        $fields = $instanceforidfield->getFieldTypes();
        $error = false;
        $idpostname = $fields[$idfieldname]->getPostName();
        $toSaveModel = null;
        if ($value['newentrycheckbox']
            || filter_var($value[$idpostname], FILTER_VALIDATE_INT) === false
            || $this->isAlwaysCreateNewInstance()) {
            //create entity or modify it
            $toSaveModel = new $this->targetEntity();
        } else {
            $options = $this->getOptions();

            if (isset($options[$value[$idpostname]])) {
                $model = BaseEntityRepository::getEntityById($this->targetEntity, $value[$idpostname]);
                if ($model != null && $model instanceof BaseEntity) {
                    $toSaveModel = $model;
                }
            }
            if ($toSaveModel == null) {
                $toSaveModel = new $this->targetEntity();
            }

        }
        /**
         * @var Field $field
         */
        foreach ($fields as $field) {
            if ($field->getSQLFieldName() == $toSaveModel->getIdFieldName()
                || $this->targetField == $field->getSQLFieldName()
                || $field instanceof DirectEditInterface) {
                if ($this->targetField == $field->getSQLFieldName()) {
                    //first determine if targetfield is arraycollection
                    $currentValue = $toSaveModel->get($this->targetField);
                    if (is_object($currentValue)) {

                        if ($currentValue instanceof \Doctrine\Common\Collections\Collection) {
                            $currentValue->add($this->sourceEntity);
                            $toSaveModel->set($this->targetField, $currentValue);
                        }
                    } elseif ($currentValue == null) {
                        $toSaveModel->set($this->targetField, $this->sourceEntity);
                    }
                }
                continue;
            }
            if ($field->validatePost($value[$field->getPostName()])) {
                $toSaveModel->set($field->getSQLFieldName(), $field->getSQLValue());
            } else {
                $this->subErrorMsg[$field->getPostName()] = $field->getErrorMsg();
                $error = true;
            }
        }

        if ($error) {
            $this->errMsg = $this->getLabel() . t(static::SUBFORMERROR);
            $this->saveSubErrorMsg();
            return false;
        }

        //persist it
        $this->getEntityManager()->persist($toSaveModel);
        //set the value
        $this->setSQLValue($toSaveModel);
        return true;
    }


    /**
     * @param $form
     * @param $clientSideValidationActivated
     * @param $fields
     * @param $value
     * @param $html
     * @param $entityForFields
     * @return string
     */
    public function getInputHtml($form, $clientSideValidationActivated = true)
    {
        $html = '';
        $html .= '
            <div class=\'subentityrowedit\'>';
        $value = $this->getSQLValue();
        $default = $this->getDefault();
        if ($value == null && $default != null) {
            $value = $default;
        }
        if ($value == null) {
            $value = new $this->targetEntity();
        }

        if ($value instanceof Proxy) {
            $value = $this->getEntityManager()
                          ->getRepository($this->targetEntity)
                          ->find($value->getId())
            ;
        }
        $this->loadSubErrorMsg();
        $classname = $this->targetEntity;

        /**
         * @var BaseEntity $entityForFields
         */
        $entityForFields = new $classname();


        //get the fields to show in the form

        $fields = $entityForFields->getFieldTypes();
//build the form

        $value->setDefaultFormViews();
        /**
         * @var Field $field
         * @var AbstractFormView $defaultFormView
         */
        $defaultFormView = $value->getDefaultFormView($form, $clientSideValidationActivated);

        if ($defaultFormView === false) {
            //display default form with all fields under each other
            foreach ($fields as $field) {
                //if id or another directedit possibility, skip (because of possible circle)
                if ($field instanceof DirectEditInterface || !$field->showInForm()) {
                    continue;
                }

                if (is_null($value)) {
                    $setValue = null;
                } else {
                    $setValue = $value->get($field->getSQLFieldName());
                }
                //set the value
                $field->setSQLValue($setValue);
                //change the post name
                $field->setPostName($this->getPostName() . "[" . $field->getPostName() . "]");

                if (isset($this->subErrorMsg[$field->getPostName()])) {
                    $field->setErrorMessage($this->subErrorMsg[$field->getPostName()]);
                }

                //get the form view
                $html .= $field->getFormView($form, $clientSideValidationActivated);
                $field->setErrorMessage(null);
            }
        } else {
            $defaultFormView->setErrorMsg($this->subErrorMsg);
            $defaultFormView->setParentPostName($this->getPostName());
            $html .= $defaultFormView->getFormView($form, $clientSideValidationActivated);

        }
        $idNewEntryCheckbox = $this->getPostName()
                              . static::REPLACE_BRACE_IN_ID_WITH
                              . "newentrycheckbox"
                              . static::REPLACE_BRACE_IN_ID_WITH;
        $nameNewEntryCheckbox = $this->getPostName() . "[newentrycheckbox]";
        if ($this->isAlwaysCreateNewInstance() !== true) {
            $html .= "
            <div class='basic-table-newentrycheckbox'>
                <label for='$idNewEntryCheckbox'>" . t("Create new entry of %s", $this->getLabel()) . "</label>
                <input type='checkbox' value='Off' id='$idNewEntryCheckbox' name='$nameNewEntryCheckbox'/>
                </div>
            ";
        }

        $html .= "<div class='parent_postname hiddenforminfo'>" . $this->getPostName() . "</div>";
        $html .= "<div class='parent_idname hiddenforminfo'>" . $this->getHtmlId() . "</div>";
        $html .= "<div class='replace_brace_in_id_with hiddenforminfo'>" . static::REPLACE_BRACE_IN_ID_WITH . "</div>";
        $html .= "<div class='prepended_before_realname hiddenforminfo'>" . static::PREPEND_BEFORE_REALNAME . "</div>";
        $html .= "<div class='options_url hiddenforminfo'>" . $this->view->action("get_options_of_field")
                 . "?fieldname=" . $this->getPostName() . "</div>";
        $html .= "<div class='options_template hiddenforminfo'>" . $entityForFields->getTypeaheadTemplate() . "</div>";
        $html .= '</div>';
        return $html;
    }
}
