<?php


namespace BasicTablePackage\Entity;


use BasicTablePackage\Test\Entity\SomeEntity;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Mapping\Column;
use ReflectionClass;
use ReflectionProperty;
use Tightenco\Collect\Support\Collection;
use function BasicTablePackage\Lib\collect as collect;

class PersistenceFieldTypeReader
{
    /**
     * @var string
     */
    private $className;

    public function __construct (string $className)
    {
        $this->className = $className;
    }

    /**
     * @return array
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function getPersistenceFieldTypes (): array
    {
        $annotationReader = new AnnotationReader();
        $someEntity = new SomeEntity();
        $reflectionClass = new ReflectionClass($someEntity);
        AnnotationRegistry::registerLoader("class_exists");
        return
            collect($reflectionClass->getProperties())
                ->keyBy(function (ReflectionProperty $reflectionProperty) { return $reflectionProperty->getName(); })
                ->map(function (ReflectionProperty $reflectionProperty) use ($annotationReader) {
                    return $annotationReader->getPropertyAnnotations($reflectionProperty);
                })
                ->map(function (array $propertyAnnotations) {
                    return collect($propertyAnnotations)->filter(function ($annotation) {
                        return $annotation instanceof Column;
                    });
                })
                ->map(function (Collection $propertyAnnotations) {
                    return collect($propertyAnnotations)
                        ->map(function (Column $column) { return $column->type; })->first();
                })->toArray();
    }
}