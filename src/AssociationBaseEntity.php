<?php

namespace Concrete\Package\BasicTablePackage\Src;

/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 28.07.16
 * Time: 10:53
 */

/**
 * Class AssociationEntity
 * class that defines associationentity. Its an Entity, which is used if a many to many relationship has to be unidirectional (in case that the inversedby side is not in the same package.
 *
 * @IgnoreAnnotation("package")\n*  Concrete\Package\BasicTablePackage\Src
 *
 */
abstract class AssociationBaseEntity extends BaseEntity
{

}