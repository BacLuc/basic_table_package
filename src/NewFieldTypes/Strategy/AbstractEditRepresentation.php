<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.04.17
 * Time: 17:02
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes\Strategy;


abstract class AbstractEditRepresentation extends AbstractFieldStrategy implements EditRepresentation
{
    protected $value;


    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getInputHtml($form, $clientSideValidationActivated)
    {
        $attributes = array(
            'title' => $this->fieldType->getPostName(),
            'value' => $this->value,
            'id'    => $this->fieldType->getHtmlId(),
        );

        if ($clientSideValidationActivated) {
            $attributes = $this->addValidationAttributes($attributes);
        }

        $value = $this->value;
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
        if (!$this->fieldType->getNullable()) {
            $attributes['required'] = 'true';
            //parsley dependendt, TODO extract
            $attributes['data-parsley-required'] = 'true';
        }

        return $attributes;
    }

    /**
     * Internal function that creates an <input> element of type $type. Handles the messiness of evaluating $valueOrMiscFields. Assigns a default class of ccm-input-$type.
     *
     * @param string $key The name/id of the element.
     * @param string $type Accepted value for HTML attribute "type"
     * @param string|array $valueOrMiscFields The value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'.
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'.
     * @param Form $form
     *
     * @return string
     */
    public static function inputType($id, $key, $type, $valueOrMiscFields, $miscFields, $form)
    {
        if (is_array($valueOrMiscFields)) {
            $value = '';
            $miscFields = $valueOrMiscFields;
        } else {
            $value = $valueOrMiscFields;
        }

        $value = h($value);

        return "<input type=\"$type\" id=\"$id\" name=\"$key\" value=\"$value\""
               . static::parseMiscFields("form-control ccm-input-$type", $miscFields) . ' />';
    }

    /**
     * Create an HTML fragment of attribute values, merging any CSS class names as necessary.
     *
     * @param string $defaultClass Default CSS class name
     * @param array $attributes A hash array of attributes (name => value), possibly including 'class'.
     *
     * @return string A fragment of attributes suitable to put inside of an HTML tag
     */
    public static function parseMiscFields($defaultClass, $attributes)
    {
        $attributes = (array)$attributes;
        if ($defaultClass) {
            $attributes['class'] =
                trim((isset($attributes['class']) ? $attributes['class'] : '') . ' ' . $defaultClass);
        }
        $attr = '';
        foreach ($attributes as $k => $v) {
            $attr .= " $k=\"$v\"";
        }

        return $attr;
    }

}