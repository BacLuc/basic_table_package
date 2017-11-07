<?php

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;

use Concrete\Core\Form\Service\Form;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\Field as Field;

/**
 *
 * Class DropdownField
 * @IgnoreAnnotation("package")\n*  Concrete\Package\BasicTablePackage\Src\FieldTypes
 * A Normal Dropdownfield for a form
 */
class DropdownField extends Field
{

    protected $options = array();

    public function __construct($sqlFieldname, $label, $postName)
    {
        parent::__construct($sqlFieldname, $label, $postName);

    }

    public function getTableView()
    {
        $options = $this->getOptions();
        return $options[$this->getSQLValue()];
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the possible Options to choose from.
     * @param array $options array of the options like array('dbvalue' => 'displayvalue')
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param Form $form
     * @param bool $clientSideValidationActivated
     * @return string
     */
    public function getFormView($form, $clientSideValidationActivated = true)
    {
        $html = "<label for='" . $this->getHtmlId() . "'>" . $this->getLabel() . "</label>";

        $html .= $this->getInputHtml($form, $clientSideValidationActivated);
        return $html;
    }

    /**
     * @param $html
     * @return string
     */
    public function getInputHtml($form, $clientSideValidationActivated = true)
    {
        $value = $this->getSQLValue();
        $default = $this->getDefault();
        if ($value == null && $default != null) {
            $value = $default;
        }
        $html = static::select($this->getHtmlId(), $this->getPostName(), $this->getOptions(), $value);

        $html .= $this->getHtmlErrorMsg();
        return $html;
    }

    /**
     * Renders a select field.
     *
     * @param string $id
     * @param string $key The name of the element. If $key denotes an array, the ID will start with $key but will have a progressive unique number added; if $key does not denotes an array, the ID attribute will be $key.
     * @param array $optionValues An associative array of key => display.
     * @param string|array $valueOrMiscFields The value of the field to be selected or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'.
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'.
     * @param Form $form
     * @return $html
     */
    public static function select($id, $key, $optionValues, $valueOrMiscFields = '', $miscFields = array())
    {
        if (!is_array($optionValues)) {
            $optionValues = array();
        }
        if (is_array($valueOrMiscFields)) {
            $selectedValue = '';
            $miscFields = $valueOrMiscFields;
        } else {
            $selectedValue = (string)$valueOrMiscFields;
        }
        if ($selectedValue !== '') {
            // $miscFields['ccm-passed-value'] = $selectedValue;
        }


        $str =
            '<select id="' . $id . '" name="' . $key . '"' . static::parseMiscFields('form-control', $miscFields) . '>';
        foreach ($optionValues as $k => $text) {
            $str .= '<option value="' . $k . '"';
            if ((string)$k === (string)$selectedValue) {
                $str .= ' selected="true"';
            }
            $str .= '>' . $text . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    public function validatePost($value)
    {
        $values = array_keys($this->getOptions());
        //check if value is integer like
        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            //if it is integer like, convert to integer
            $value = intval($value);
        }
        if (in_array($value, $values, true)) {
            return parent::validatePost($value);
        } elseif (in_array($value, $this->getOptions(), true)) {
            $reversedOptions = array_flip($this->getOptions());
            $value = $reversedOptions[$value];
            return parent::validatePost($value);
        } else {
            if ($this->nullable !== false && $value == null) {
                $this->value = $value;
                return true;
            }
            return false;
        }
    }


}
