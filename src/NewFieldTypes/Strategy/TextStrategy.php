<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.04.17
 * Time: 17:29
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes\Strategy;


class TextStrategy extends AbstractEditRepresentation implements Validator, EditRepresentation, ViewRepresentation
{

    protected $errMsg;

    public function getEditRepresentation($form, $clientSideValidationActivated)
    {
        $returnString =
            "<label for='" . $this->fieldType->getHtmlId() . "'>" . $this->fieldType->getLabel() . "</label>";

        $returnString .= $this->getInputHtml($form, $clientSideValidationActivated);
        return $returnString;
    }

    public function getInputHtml($form, $clientSideValidationActivated)
    {
        $attributes = array(
            'title' => $this->fieldType->getPostName(),
            'value' => $this->fieldType->getSQLValue(),
            'id'    => $this->fieldType->getHtmlId(),
        );

        if ($clientSideValidationActivated) {
            $attributes = $this->addValidationAttributes($attributes);
        }

        $value = $this->getSQLValue();
        $default = $this->fieldType->getDefault();
        if ($value == null && $default != null) {
            $value = $default;
        }
        /**
         * @var Form $form
         */
        $returnString =
            static::inputType($this->fieldType->getHtmlId(), $this->fieldType->getPostName(), "text", $value,
                $attributes, $form);
        $returnString .= $this->fieldType->getHtmlErrorMsg();
        return $returnString;
    }

    public function addValidationAttributes($attributes)
    {
        return parent::addValidationAttributes($attributes);
    }

    public function getSQLValue()
    {
        return $this->value;
    }

    public function setSQLValue($value)
    {
        $this->setValue($value);
    }

    public function setValue($value)
    {
        if (is_object($value) || is_array($value)) {
            throw new \InvalidArgumentException("%s can only accept primitive types", __CLASS__);
        }
        $this->value = $value;
    }

    public function validatePost($value)
    {
        if (!$this->fieldType->getNullable() && strlen($value) == 0) {
            $this->errMsg = $this->fieldType->getLabel() . $this->t(static::NULLERRORMSG);
            return false;
        }

        $this->value = $value;
        return true;
    }

    public function getErrorMsg()
    {
        return $this->errMsg;
    }

    public function getViewRepresentation()
    {
        return $this->value;
    }

    /**
     * @param $text
     * @return string
     */
    protected function t ($text): string
    {
        return t($text);
    }


}