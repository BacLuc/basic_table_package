<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.04.17
 * Time: 16:01
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes;


use Concrete\Package\BasicTablePackage\Src\FieldTypes\FieldTypeInterface;

interface FieldType extends FieldTypeInterface
{

    public function setSQLValue($value);

    public function setLabel($label);

    public function setPostName($postname);

    public function getSQLValue();

    public function getTableView();

    /**
     * @param $default
     * @return $this
     */
    public function setDefault($default);

    public function getDefault();

    public function addValidationAttributes($attributes);

    public function getFormView($form, $clientSideValidationActivated = true);

    public function getHtmlErrorMsg();

    public function getLabel();


    public function getPostName();

    public function getSQLFieldName();

    public function validatePost($value);

    public function getErrorMsg();

    public function showInForm();

    public function showInTable();

    /**
     * @param boolean $showInForm
     * @return $this
     */
    public function setShowInForm($showInForm);

    /**
     * @param boolean $showInTable
     * @return $this
     */
    public function setShowInTable($showInTable);

    /**
     * set the error message to display
     * @param string $msg
     * @return $this
     */
    public function setErrorMessage($msg);

    /**
     * @param boolean $nullable
     * @return $this
     */
    public function setNullable($nullable);

    public function getNullable();


    public function getHtmlId();

    /**
     * @return bool
     */
    public function isNotSet();

    /**
     * @param bool $notSet
     */
    public function setNotSet($notSet);
}