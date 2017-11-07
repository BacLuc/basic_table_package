<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 22.09.16
 * Time: 21:59
 */

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;


class EmailField extends Field
{
    const EMAILFORMATERROR = " has to be a valid email address";


    public function getFormView($form, $clientSideValidationActivated = true)
    {
        $returnString = "<label for='" . $this->getPostName() . "'>" . $this->getLabel() . "</label>";
        $returnString .= $this->getInputHtml($form, $clientSideValidationActivated);
        return $returnString;
    }

    /**
     * @param $form
     * @param $clientSideValidationActivated
     * @param $returnString
     * @return string
     */
    public function getInputHtml($form, $clientSideValidationActivated)
    {
        $value = $this->getSQLValue();
        $default = $this->getDefault();
        if ($value == null && $default != null) {
            $value = $default;
        }

        $attributes = array(
            'title' => $this->getPostName(),
            'value' => $value,
            'id'    => $this->getHtmlId(),
        );

        if ($clientSideValidationActivated) {
            $attributes = $this->addValidationAttributes($attributes);
        }


        $returnString = static::inputType($this->getHtmlId(), $this->getPostName(), "text", $value, $attributes, $form);

        $returnString .= $this->getHtmlErrorMsg();
        return $returnString;
    }

    public function addValidationAttributes($attributes)
    {
        $attributes = parent::addValidationAttributes($attributes); // TODO: Change the autogenerated stub
        $attributes['type'] = "email";
        $attributes['data-parsley-type'] = "email";
        return $attributes;
    }

    public function validatePost($value)
    {
        if (!$this->nullable && strlen($value) == 0) {
            $this->errMsg = $this->getLabel() . t(static::NULLERRORMSG);
            return false;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errMsg = $this->getLabel() . t(static::EMAILFORMATERROR);
            return false;

        }

        $this->value = $value;
        return true;
    }
}