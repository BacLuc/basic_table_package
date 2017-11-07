<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 22.08.16
 * Time: 19:01
 */

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;


use Concrete\Package\BasicTablePackage\Src\AbstractFormView;
use Concrete\Package\BasicTablePackage\Src\BaseEntity;
use Concrete\Package\BasicTablePackage\Src\BaseEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

class DirectEditAssociatedEntityMultipleField extends AbstractDirectEditField
{


    public function __construct($sqlFieldname, $label, $postName)
    {
        parent::__construct($sqlFieldname, $label, $postName);
        $this->default = new ArrayCollection();
    }


    public function validatePost($value)
    {
        if ($value == null || !is_array($value)) {
            $this->setSQLValue(new ArrayCollection());
        }


        //for first version, create for every post new entity, and persist it
        $collectarraycollection = new ArrayCollection();
        $error = false;

        /**
         * @var BaseEntity $instanceforidfield ;
         */
        $instanceforidfield = new $this->targetEntity();
        $idfieldname = $instanceforidfield->getIdFieldName();

        if (count($value) > 0) {
            foreach ($value as $rownum => $postvalues) {
                if (!is_array($postvalues)) {
                    continue;
                }


                $fields = $instanceforidfield->getFieldTypes();
                $idpostname = $fields[$idfieldname]->getPostName();
                $toSaveModel = null;
                if ($postvalues['newentrycheckbox']
                    || filter_var($postvalues[$idpostname], FILTER_VALIDATE_INT) === false
                    || $this->isAlwaysCreateNewInstance()) {
                    //create entity or modify it
                    $toSaveModel = new $this->targetEntity();
                } else {
                    $options = $this->getOptions();

                    if (isset($options[$postvalues[$idpostname]])) {
                        $model = BaseEntityRepository::getEntityById($this->targetEntity, $postvalues[$idpostname]);
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
                        || $field instanceof DirectEditInterface
                    ) {
                        //$toSaveModel->set($this->targetField,$this->sourceEntity);
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
                    if ($field->validatePost($postvalues[$field->getPostName()])) {
                        $toSaveModel->set($field->getSQLFieldName(), $field->getSQLValue());
                    } else {
                        $this->subErrorMsg[$rownum][$field->getPostName()] = $field->getErrorMsg();
                        $error = true;
                    }
                }

                if ($error) {
                    $this->errMsg = $this->getLabel() . t(DirectEditAssociatedEntityField::SUBFORMERROR);
                    $this->saveSubErrorMsg();
                    return false;
                }


                //persist it

                $this->getEntityManager()->persist($toSaveModel);
                $collectarraycollection->add($toSaveModel);
            }
        }
        //set the value
        $this->setSQLValue($collectarraycollection);
        return true;

    }

    public function setSQLValue($value)
    {
        if ($value == null) {
            $value = new $this->getDefault();
            return $this;
        }
        if ($value instanceof \Doctrine\Common\Collections\Collection) {

            if (count($value) == 0) {
                //add empty collection
                $this->value = $value;
                return $this;
            }
            foreach ($value->getIterator() as $num => $item) {
                if (!($item instanceof $this->targetEntity)) {
                    throw new \InvalidArgumentException(
                        sprintf("Element in given collection was of type %s, but only elements of type %s are allowed"
                            , get_class($item), $this->targetEntity)
                    );
                }
            }
            $this->value = $value;
            return $this;
        }
    }

    /**
     * @param $form
     * @param $clientSideValidationActivated
     * @return string
     */
    public function getInputHtml($form, $clientSideValidationActivated = true)
    {
        /**
         * @var BaseEntity $value
         */
        $values = $this->getSQLValue();

        if (($values == null || count($values) == 0) && count($this->getDefault()) > 0) {
            $values = $this->getDefault();
        }

        $this->loadSubErrorMsg();

        if ($values instanceof \Doctrine\Common\Collections\Collection) {
            $values = $values->toArray();
        }
        $html = '';
        if (count($values) > 0) {
            $html .= "<button type='button' value='' class='btn bacluc-inlineform actionbutton add'><i class ='fa fa-plus'></i></button>";
        }

        $classname = $this->targetEntity;

        /**
         * @var BaseEntity $entityForFields
         */
        $entityForFields = new $classname();


        //get the fields to show in the form

        $fields = $entityForFields->getFieldTypes();


        $html .= "<div class='bacluc-realrows '>";
        //build the forms
        $rownum = 0;
        if (count($values) > 0) {

            foreach ($values as $value) {
                $html .= "<div class='col-xs-12'>
                    <div class='subentityrowedit  row'>
                ";

                if ($value instanceof Proxy) {
                    $value = $this->getEntityManager()
                                  ->getRepository($this->targetEntity)
                                  ->find($value->getId())
                    ;
                }
                $value->setDefaultFormViews();

                /**
                 * @var AbstractFormView $defaultFormView
                 */
                $defaultFormView = $value->getDefaultFormView($form, $clientSideValidationActivated);

                if ($defaultFormView === false) {
                    /**
                     * @var Field $field
                     */
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

                        $oldpostname = $field->getPostName();
                        //change the post name
                        $field->setPostName($this->getPostName() . "[" . $rownum . "][" . $field->getPostName() . "]");

                        if (isset($this->subErrorMsg[$rownum][$oldpostname])) {
                            $field->setErrorMessage($this->subErrorMsg[$rownum][$oldpostname]);
                        }

                        //get the form view
                        $html .= $field->getFormView($form, $clientSideValidationActivated);
                        //reset the post name
                        $field->setPostName($oldpostname);
                        $field->setErrorMessage(null);
                    }
                } else {
                    $defaultFormView->setErrorMsg($this->subErrorMsg[$rownum]);
                    $defaultFormView->setParentPostName($this->getPostName() . "[" . $rownum . "]");
                    $html .= $defaultFormView->getFormView($form, $clientSideValidationActivated);

                }
                $idNewEntryCheckbox = $this->getPostName()
                                      . static::REPLACE_BRACE_IN_ID_WITH
                                      . $rownum
                                      . static::REPLACE_BRACE_IN_ID_WITH . static::REPLACE_BRACE_IN_ID_WITH
                                      . "newentrycheckbox" . static::REPLACE_BRACE_IN_ID_WITH;
                $nameNewEntryCheckbox = $this->getPostName() . "[" . $rownum . "][newentrycheckbox]";

                if ($this->isAlwaysCreateNewInstance() !== true) {
                    $html .= "
                    <div class='basic-table-newentrycheckbox'>
                    <label for=''>" . t("Create new entry of %s", $this->getLabel()) . "</label>
                    <input type='checkbox' value='Off' id='$idNewEntryCheckbox' name='$nameNewEntryCheckbox'/>
                    </div>
                    ";
                }
                //add delete button
                $html .= "<button type='button' value='' class='btn bacluc-inlineform actionbutton delete'><i class ='fa fa-trash'></i></button>";

                //close row
                $html .= "</div>
                </div>";
                $rownum++;


            }

        }
        $html .= "</div>";

        //now add empty hidden rowform to add
        $html .= "<div class='hidden_form_row'> ";
        $html .= "<div class='col-xs-12'>
                    <div class='subentityrowedit row'>
                ";

        $emptyEdit = new $classname();
        $emptyEdit->setDefaultFormViews();

        /**
         * @var AbstractFormView $defaultFormView
         */
        $defaultFormView = $emptyEdit->getDefaultFormView($form, $clientSideValidationActivated);
        if ($defaultFormView === false) {
            foreach ($fields as $field) {
                //if id or another directedit possibility, skip (because of possible circle)
                if ($field instanceof DirectEditInterface || !$field->showInForm()) {


                    continue;
                }
                //set the value
                $field->setSQLValue(null);
                //change the post name
                $field->setPostName(static::PREPEND_BEFORE_REALNAME . $field->getPostName());

                //get the form view
                $html .= $field->getFormView($form, $clientSideValidationActivated);
            }
        } else {
            $defaultFormView->setParentPostName(static::PREPEND_BEFORE_REALNAME);
            $html .= $defaultFormView->getFormView($form, $clientSideValidationActivated);
        }
        $idNewEntryCheckbox = static::PREPEND_BEFORE_REALNAME . "newentrycheckbox";
        $nameNewEntryCheckbox = static::PREPEND_BEFORE_REALNAME . "newentrycheckbox";

        if ($this->isAlwaysCreateNewInstance() !== true) {
            $html .= "
                    <div class='basic-table-newentrycheckbox'>
                    <label for='$idNewEntryCheckbox'>" . t("Create new entry of %s", $this->getLabel()) . "</label>
                    <input type='checkbox' value='Off' id='$idNewEntryCheckbox' name='$nameNewEntryCheckbox'/>
                    </div>
                    ";
        }
        $html .= "<button type='button' value='' class='btn bacluc-inlineform actionbutton delete'><i class ='fa fa-trash'></i></button>";

        //now add information to add new row
        $html .= "<div class='rownum hiddenforminfo'>" . $rownum . "</div>";
        $html .= "<div class='parent_postname hiddenforminfo'>" . $this->getPostName() . "</div>";
        $html .= "<div class='parent_idname hiddenforminfo'>" . $this->getHtmlId() . "</div>";
        $html .= "<div class='replace_brace_in_id_with hiddenforminfo'>" . static::REPLACE_BRACE_IN_ID_WITH . "</div>";
        $html .= "<div class='prepended_before_realname hiddenforminfo'>" . static::PREPEND_BEFORE_REALNAME . "</div>";
        $html .= "<div class='options_url hiddenforminfo'>" . $this->view->action("get_options_of_field")
                 . "?fieldname=" . $this->getPostName() . "</div>";
        $html .= "<div class='options_template hiddenforminfo'>" . $entityForFields->getTypeaheadTemplate() . "</div>";

        $html .= "</div>
        </div>";
        $html .= "</div>";

        //now add add button
        $html .= "<button type='button' value='' class='btn bacluc-inlineform actionbutton add'><i class ='fa fa-plus'></i></button>";


        $html .= "";


        $html .= $this->getHtmlErrorMsg();
        return $html;
    }

    public function getTableView()
    {
        $values = $this->getSQLValue();
        if ($values instanceof PersistentCollection || $values instanceof ArrayCollection) {
            $values = $values->toArray();
        }

        $string = "";
        if (is_array($values)) {
            $first = true;

            foreach ($values as $valuenum => $value) {
                $appendString = "";
                if (is_object($value)) {
                    $classname = get_class($value);
                    if ($value instanceof BaseEntity) {
                        $function = $classname::getDefaultGetDisplayStringFunction();
                        $appendString = $function($value);
                    }
                } else {
                    $appendString = $value;
                }

                if ($first) {
                    $first = false;
                } else {
                    $string .= ", ";
                }
                $string .= $appendString;
            }
        }
        return $string;
    }


}
