# DiscriminatorEntry 
Because in Doctrine you have to define the discriminator map for all future child entity classes,
i implemented this DiscriminatorEntry which was inspired by  
It works like this:  
#### [DiscriminatorEntry.php](DiscriminatorEntry.php)  
You can attach EventListeners to the Entity Manager. The EventListener we need is the DiscriminatorListener,
which is attached to the  \Doctrine\ORM\Events::loadClassMetadata Event. It is added in the function
[\Concrete\Package\BasicTablePackage\Controller::addDiscriminatorListenerToEm](../../README.md#controlleradddiscriminatorlistenertoem).
As soon as the Metadata of a class is loaded, the DiscriminatorListener::loadClassMetadata function is called,
which generates the DiscriminatorMap for the family (parents and childs) of the class.  And because the wrong discriminator map
with only the Topmost parent class before BaseEntity was already generated and cached, this cache has to deleted and saved again in the function
DiscriminatorListener::overrideMetadata, for the whole family (in case a new Child class was found.)
The other functions in the DiscriminatorEntry are just helper functions to navigate through the class hierarchy and to extract the value of the Annotation @DiscriminatorEntry
 (which you have to add to every entity class)
 
#### [Annotation.php](Annotation.php)  
The Class Annotation is needed to read the Annotation of the class. It is a wrapper for the class \Doctrine\Common\Annotations\AnnotationReader.
It bundles the two lines of the function getAnnotationsForClass, and makes it possible that we use the AnnotationReader instead of the SimpleAnnotationReader Concrete5 uses (to which we could not add the Annotation DiscriminatorEntry) 
```php
<?php
class Annotation
{

    /**
     * @var AnnotationReader
     */
    public static $reader;

    public static function getAnnotationsForClass( $className ) {
        $class = new \ReflectionClass( $className );
        return Annotation::$reader->getClassAnnotations( $class );
    }

}
//set the reader. Externally, because you cannot set a static property as a return value of a function
Annotation::$reader = new AnnotationReader();
//this functions makes sure, that this namespace is also scanned for
// Classes who match certain Annotations, in our case DiscriminatorEntry
AnnotationRegistry::registerAutoloadNamespaces( array(__NAMESPACE__ . "" ));
```


#### [DiscriminatorEntry.php](DiscriminatorEntry.php)  
The class DiscriminatorEntry is used to parse the Annotation @DiscriminatorEntry. It consists of a property value, a getter and a setter method.
Most important is the Annotation @Annotation, which tells Doctrine that this class which defines an Annotation.
