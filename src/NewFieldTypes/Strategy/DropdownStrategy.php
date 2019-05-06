<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.04.17
 * Time: 19:30
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes\Strategy;


use Concrete\Core\Html\Object\Collection;

class DropdownStrategy extends TextStrategy implements Validator, ViewRepresentation, EditRepresentation
{
    protected $options = array();


    public function getEditRepresentation($form, $clientSideValidationActivated)
    {
        $html = "<label for='" . $this->fieldType->getHtmlId() . "'>" . $this->fieldType->getLabel() . "</label>";

        $html .= $this->getInputHtml($form, $clientSideValidationActivated);
        return $html;
    }

    public function getInputHtml($form, $clientSideValidationActivated)
    {
        $value = $this->getSQLValue();
        $default = $this->fieldType->getDefault();
        if ($value == null && $default != null) {
            $value = $default;
        }
        $html =
            static::select($this->fieldType->getHtmlId(), $this->fieldType->getPostName(), $this->getOptions(), $value);

        $html .= $this->fieldType->getHtmlErrorMsg();
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

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the possible Options to choose from.
     * @param array $options array of the options like array('dbvalue' => 'displayvalue')
     * @return $this
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !($options instanceof Collection) && !($options instanceof \Iterator)) {
            ob_start();
            var_dump($options);
            $toDisplay = ob_get_clean();
            throw new \InvalidArgumentException(sprintf('$options has to be an array, a Collection or an Iterator that it can be converted to an array, was %s',
                $toDisplay));
        }
        $this->options = $options;
        return $this;

    }

    public function setSQLValue($value)
    {
        if (in_array($value, array_keys($this->getOptions())) === false) {
            if ($this->fieldType->getNullable() && strlen($value) == 0) {

            } else {
                throw new \InvalidArgumentException(sprintf('$value has to be one of %s, was %s',
                    implode(", ", $this->getOptions()), $value));
            }
        }
        $this->value = $value;
        return $this;

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
            $this->value = $value;
            return true;
        } elseif (in_array($value, $this->getOptions(), true)) {
            $reversedOptions = array_flip($this->getOptions());
            $value = $reversedOptions[$value];
            $this->value = $value;
            return true;
        } else {
            if ($this->fieldType->getNullable() !== false && $value == null) {
                $this->value = $value;
                return true;
            }
            return false;
        }
    }

    public function getViewRepresentation()
    {
        $options = $this->getOptions();
        return $options[$this->getSQLValue()];
    }

}