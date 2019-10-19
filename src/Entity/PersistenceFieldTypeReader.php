<?php


namespace BasicTablePackage\Entity;


use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
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
     * @throws \RuntimeException
     */
    public function getPersistenceFieldTypes (): array
    {
        try {
            $annotationReader = new AnnotationReader();
            $entity = new $this->className();
            $reflectionClass = new ReflectionClass($entity);
        }
        catch (\ReflectionException | AnnotationException $e) {
            throw new \RuntimeException($e);
        }
        return
            collect($reflectionClass->getProperties())
                ->keyBy(function (ReflectionProperty $reflectionProperty) { return $reflectionProperty->getName(); })
                ->filter(function ($__, $key) { return $key !== "id"; })
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