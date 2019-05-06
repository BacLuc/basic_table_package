<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 19.10.16
 * Time: 16:49
 */

namespace Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Debug\Exception\ClassNotFoundException;

/**
 * Class Annotation
 * @package Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry
 */
class Annotation
{

    /**
     * @var AnnotationReader
     */
    public static $reader;

    public static function getAnnotationsForClass($className)
    {
        $class = new \ReflectionClass($className);
        return Annotation::$reader->getClassAnnotations($class);
    }

}

//set the reader. Externally, because you cannot set a static property as a return value of a function
Annotation::$reader = new AnnotationReader();
//the function with the default namespace does not exist anymore
AnnotationRegistry::registerAutoloadNamespaces(
    array(
        __NAMESPACE__ . "",
        __NAMESPACE__ . "" => array(__DIR__), //@see strange AnnotationRegistry::loadAnnotationClass
    )

);

AnnotationRegistry::registerLoader(function ($class) {
    if (strpos($class, "Concrete\\Package") === false) {
        return false;
    }
    try {
        $class = new $class();
        return true;
    } catch (\Exception $e) {
        if ($e instanceof ClassNotFoundException) {

        } else {
            return true;
        }
    }
    return false;
});