<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 22.08.16
 * Time: 21:13
 */

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;


interface DirectEditInterface extends AssociationFieldInterface
{

    const PREPEND_BEFORE_REALNAME = "bacluc_inline_form";
}