<?php


namespace BasicTablePackage\FieldTypeDetermination;


use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Annotation;
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
    /**
     * @var PersistenceFieldTypeHandler[]
     */
    private $persistenceFieldTypeHandlers;

    public function __construct(string $className, array $persistenceFieldTypeHandlers)
    {
        $this->className = $className;
        $this->persistenceFieldTypeHandlers = $persistenceFieldTypeHandlers;
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    public function getPersistenceFieldTypes(): array
    {
        try {
            $annotationReader = new AnnotationReader();
            $entity = new $this->className();
            $reflectionClass = new ReflectionClass($entity);
        } catch (\ReflectionException | AnnotationException $e) {
            throw new \RuntimeException($e);
        }
        return
            collect($reflectionClass->getProperties())
                ->keyBy(function (ReflectionProperty $reflectionProperty) {
                    return $reflectionProperty->getName();
                })
                ->filter(function ($__, $key) {
                    return $key !== "id";
                })
                ->map(function (ReflectionProperty $reflectionProperty) use ($annotationReader) {
                    return $annotationReader->getPropertyAnnotations($reflectionProperty);
                })
                ->map(function (array $propertyAnnotations) {
                    return collect($propertyAnnotations)->filter(function (Annotation $annotation) {
                        return collect($this->persistenceFieldTypeHandlers)
                                   ->filter(function (PersistenceFieldTypeHandler $handler) use ($annotation) {
                                       return $handler->canHandle($annotation);
                                   })->count() > 0;
                    });
                })
                ->map(function (Collection $propertyAnnotations) {
                    return collect($propertyAnnotations)
                        ->map(function (Annotation $annotation) {
                            /**@var PersistenceFieldTypeHandler $handler */
                            $handler = collect($this->persistenceFieldTypeHandlers)
                                ->filter(function (PersistenceFieldTypeHandler $handler) use ($annotation) {
                                    return $handler->canHandle($annotation);
                                })->first();
                            return $handler->getFieldTypeOf($annotation);
                        })->first();
                })->toArray();
    }
}