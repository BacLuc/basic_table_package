<?php


namespace BasicTablePackage\Test\FieldTypeDetermination;


use BasicTablePackage\FieldTypeDetermination\ColumnAnnotationHandler;
use BasicTablePackage\FieldTypeDetermination\ManyToManyAnnotationHandler;
use BasicTablePackage\FieldTypeDetermination\ManyToOneAnnotationHandler;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldType;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypeReader;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypes;
use BasicTablePackage\Test\Entity\InMemoryRepositoryFactory;
use BasicTablePackage\Test\Entity\SomeEntity;
use PHPUnit\Framework\TestCase;
use function BasicTablePackage\Lib\collect as collect;

class PersistenceFieldTypeReaderTest extends TestCase
{

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function test_read_property_types()
    {
        $repositoryFactory = new InMemoryRepositoryFactory();
        $persistenceFieldTypeReader =
            new PersistenceFieldTypeReader(SomeEntity::class,
                [
                    new ColumnAnnotationHandler(),
                    new ManyToOneAnnotationHandler($repositoryFactory),
                    new ManyToManyAnnotationHandler($repositoryFactory)
                ]);
        $persistenceFieldTypes = $persistenceFieldTypeReader->getPersistenceFieldTypes();
        $types = collect($persistenceFieldTypes)->map(function (PersistenceFieldType $value) {
            return $value->getType();
        })->toArray();
        self::assertThat($types,
            self::equalTo([
                "value"          => PersistenceFieldTypes::STRING,
                "intcolumn"      => PersistenceFieldTypes::INTEGER,
                "datecolumn"     => PersistenceFieldTypes::DATE,
                "datetimecolumn" => PersistenceFieldTypes::DATETIME,
                "wysiwygcolumn"  => PersistenceFieldTypes::TEXT,
                "manyToOne"      => PersistenceFieldTypes::MANY_TO_ONE,
                "manyToMany"     => PersistenceFieldTypes::MANY_TO_MANY
            ]));
    }

}