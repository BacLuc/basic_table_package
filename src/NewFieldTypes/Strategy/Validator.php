<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.04.17
 * Time: 16:02
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes\Strategy;


interface Validator
{
    const NULLERRORMSG = ' cannot be empty.';

    /**
     * @param $value
     * @return $this
     */
    public function setSQLValue($value);

    /**
     * @return mixed
     */
    public function getSQLValue();

    /**
     * @param $value
     * @return bool
     */
    public function validatePost($value);

    /**
     * @return string
     */
    public function getErrorMsg();


}