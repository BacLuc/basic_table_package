<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.04.17
 * Time: 16:03
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes\Strategy;


interface EditRepresentation
{
    /**
     * @param array $attributes
     * @return array
     */
    public function addValidationAttributes($attributes);

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @param $form
     * @param $clientSideValidationActivated
     * @return mixed
     */
    public function getEditRepresentation($form, $clientSideValidationActivated);

    public function getInputHtml($form, $clientSideValidationActivated);

}