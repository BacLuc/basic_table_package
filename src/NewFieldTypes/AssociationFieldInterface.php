<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 24.03.17
 * Time: 17:28
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes;


interface AssociationFieldInterface
{

    public function setLinkInfo(
        $sourceEntity,
        $sourceField,
        $targetEntity,
        $targetField = null,
        $associationType = null,
        callable $getDisplayString = null,
        callable $filter = null
    );

    public function setGetDisplayString(callable $getDisplayString);

    public function setFilter(callable $filter);

    public function getFilter();

    public function getSourceEntity();

    public function getSourceField();

    public function getTargetEntity();

    public function getTargetField();

    public function getGetDisplayStringFunction();

    public function setAssociationType($type);

    public function getAssociationType();


}