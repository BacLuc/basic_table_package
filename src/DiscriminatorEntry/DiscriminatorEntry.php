<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 19.10.16
 * Time: 16:48
 */

namespace Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry;

/**
 * Class DiscriminatorEntry
 * package Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry
 * added Target Annotation that it only should have effect on class annotations
 * if you want to use an attribute, you need to define it, and the type, else you have an exception
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("value", type = "string"),
 * })
 */
class DiscriminatorEntry
{
    private $value;

    public function __construct(array $data)
    {
        $this->value = $data['value'];
    }

    public function getValue()
    {
        return $this->value;
    }
}