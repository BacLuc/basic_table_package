<?php

namespace Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry;

use Concrete\Package\BasicTablePackage\Src\BaseEntity;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * This Listener listens to the loadClassMetadata event. Upon this event
 * it hooks into Doctrine to update discriminator maps. Adding entries
 * to the discriminator map at parent level is just not nice. We turn this
 * around with this mechanism. In the subclass you will be able to give an
 * entry for the discriminator map. In this listener we will retrieve the
 * load metadata event to update the parent with a good discriminator map,
 * collecting all entries from the subclasses.
 */
class DiscriminatorListener implements \Doctrine\Common\EventSubscriber
{

    // The driver of Doctrine, can be used to find all loaded classes
    const ENTRY_ANNOTATION = 'Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry\DiscriminatorEntry';

    // The *temporary* map used for one run, when computing everything
    private $driver;

    // The cached map, this holds the results after a computation, also for other classes
    private $map;
    private $cachedMap;

    public function __construct(\Doctrine\ORM\EntityManager $db)
    {
        $this->driver = $db->getConfiguration()->getMetadataDriverImpl();
        $this->cachedMap = Array();
    }

    public function getSubscribedEvents()
    {
        return Array(\Doctrine\ORM\Events::loadClassMetadata);
    }

    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $event)
    {
        // Reset the temporary calculation map and get the classname
        $this->map = Array();
        $class = $event->getClassMetadata()->name;

        $reflection = new \ReflectionClass($class);
        if (!$reflection->isSubclassOf("Concrete\\Package\\BasicTablePackage\\Src\\BaseEntity")) {
            return;
        }

        // Did we already calculate the map for this element?
        if (array_key_exists($class, $this->cachedMap)) {
            $this->overrideMetadata($event, $class);
            return;
        }
        $extractEntry = $this->extractEntry($class);
        $discriminatorMap = array_flip($event->getClassMetadata()->discriminatorMap);
        $discrCurrent = $discriminatorMap[$class];
        $shouldBeMapValue = $this->map;
        $discrShouldBe = $shouldBeMapValue[$class];

        // Do we have to process this class?
        if (($discrCurrent == null || $discrCurrent != $discrShouldBe)

            && $extractEntry) {
            // Now build the whole map
            $this->checkFamily($class);
        } else {
            // Nothing to do…
            return;
        }

        // Create the lookup entries
        $dMap = array_flip($this->map);
        foreach ($this->map as $cName => $discr) {
            $this->cachedMap[$cName]['map'] = $dMap;
            $this->cachedMap[$cName]['discr'] = $this->map[$cName];
        }

        // Override the data for this class
        $this->overrideMetadata($event, $class);
    }

    private function overrideMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $event, $class, $recursive = false)
    {
        // Set the discriminator map and value
        $event->getClassMetadata()->discriminatorMap = $this->cachedMap[$class]['map'];
        $event->getClassMetadata()->discriminatorValue = $this->cachedMap[$class]['discr'];

        // If we are the top-most parent, set subclasses!
        if (isset($this->cachedMap[$class]['isParent']) && $this->cachedMap[$class]['isParent'] === true) {
            $subclasses = $this->cachedMap[$class]['map'];
            unset($subclasses[$this->cachedMap[$class]['discr']]);
            if (!is_array($subclasses)) {
                $subclasses = array();
            }
            $event->getClassMetadata()->subClasses = array_values($subclasses);
        }

        //override metadata of parent too
        $rc = new \ReflectionClass($class);
        $parentClass = $rc->getParentClass();

        if ($parentClass != false) {
            $parent = $parentClass->getName();

            try {
                $cacheDriver = $event->getEntityManager()->getMetadataFactory()->getCacheDriver();

                if ($parent == BaseEntity::class) {

                } else {
                    $PersistentMetadataParent =
                        $event->getEntityManager()->getMetadataFactory()->getMetadataFor($parent);
                    $eventArgs = new LoadClassMetadataEventArgs($PersistentMetadataParent, $event->getEntityManager());
                    $this->overrideMetadata($eventArgs, $parent, true);
                    $this->cachedMap[$parent]['map'] = $this->cachedMap[$class]['map'];
                    $PersistentMetadataParent->discriminatorMap = $this->cachedMap[$class]['map'];
                    $event->getEntityManager()->getMetadataFactory()
                          ->setMetadataFor($parent, $PersistentMetadataParent)
                    ;
                    //because doctrine caches before we refactor the discriminatorMap,
                    //we override the cache
                    $cacheDriver->save(
                        $parent . '$CLASSMETADATA',
                        $PersistentMetadataParent,
                        null
                    );
                }
                if ($recursive === false) {
                    $cacheDriver->save(
                        $class . '$CLASSMETADATA',
                        $event->getClassMetadata(),
                        null
                    );
                }
                //refresh cache

            } catch (\Exception $e) {

            }
        }
    }

    private function extractEntry($class)
    {

        $annotations = Annotation::getAnnotationsForClass($class);
        /*
         * getAnnotationsForClass gives back an array like this. There is no key called class
         * Array
(
    [0] => Doctrine\ORM\Mapping\Entity Object
        (
            [repositoryClass] =>
            [readOnly] =>
        )

    [1] => Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry\DiscriminatorEntry Object
        (
            [value:Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry\DiscriminatorEntry:private] => Concrete\Package\BaclucPersonPackage\Src\PostalAddress
        )

    [2] => Doctrine\ORM\Mapping\Table Object
        (
            [name] => bacluc_postal_address
            [schema] =>
            [indexes] =>
            [uniqueConstraints] =>
            [options] => Array
                (
                )

        )

)

         * */
        $success = false;
        foreach ($annotations as $key => $annotation) {
            if (get_class($annotation) == self::ENTRY_ANNOTATION) {
                //TODO check for duplicates
                $this->map[$class] = $annotation->getValue();
                $success = true;
            }
        }
//       if (is_array($annotations['class'])) {
//            if (array_key_exists(self::ENTRY_ANNOTATION, $annotations['class'])) {
//                $value = $annotations['class'][self::ENTRY_ANNOTATION]->value;
//
//                if (in_array($value, $this->map)) {
//                    throw new \Exception("Found duplicate discriminator map entry '" . $value . "' in " . $class);
//                }
//
//                $this->map[$class] = $value;
//                $success = true;
//            }
//        }

        return $success;

    }

    private function checkFamily($class)
    {
        $rc = new \ReflectionClass($class);
        $parent = $rc->getParentClass()->name;

        if ($parent !== null && $parent != BaseEntity::class) { //if no parent class is there, its null, not false
            // Also check all the children of our parent
            //but only if not BaseEntity
            $this->checkFamily($parent);


        } else {
            // This is the top-most parent, used in overrideMetadata
            $this->cachedMap[$class]['isParent'] = true;

            // Find all the children of this class
            $this->checkChildren($class);
        }
    }

    private function checkChildren($class)
    {
        /*
        Because $this->driver->getAllClassNames() did not work, implemented own method to get subclasses
        attention, getSubClasses returns all Child Classes, not only the direct Child classes
        */
        foreach (static::getSubClasses($class) as $name) {
            $cRc = new \ReflectionClass($name);
            $cParent = $cRc->getParentClass()->name;

            // Haven't done this class yet? Go for it.
            //removed the check if it is a direct child. It does not really matter (and didn't work somehow)
            if (!array_key_exists($name, $this->map) && $this->extractEntry($name)) {
                $this->checkChildren($name);
            }
        }
    }

    /**
     *
     * @param $class
     * @return array
     */
    public static function getSubClasses($class)
    {
        $subclasses = array();

        foreach (get_declared_classes() as $potentialSubclass) {

            $reflection = new \ReflectionClass($potentialSubclass);
            if ($reflection->isSubclassOf($class)) {
                $subclasses[] = $potentialSubclass;
            }

        }
        return $subclasses;
    }

}