<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.04.17
 * Time: 16:03
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes\Strategy;


Interface ViewRepresentation
{
    public function setValue($value);

    public function getViewRepresentation();
}