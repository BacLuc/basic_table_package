<?php


namespace BaclucC5Crud\Test\FieldTypeDetermination;


use BaclucC5Crud\FieldTypeDetermination\ColumnAnnotationHandler;
use BaclucC5Crud\FieldTypeDetermination\ManyToManyAnnotationHandler;
use BaclucC5Crud\FieldTypeDetermination\ManyToOneAnnotationHandler;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldType;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypeReader;
use BaclucC5Crud\FieldTypeDetermination\PersistenceFieldTypes;
use BaclucC5Crud\Test\Entity\InMemoryRepositoryFactory;
use BaclucC5Crud\Test\Entity\SomeEntity;
use PHPUnit\Framework\TestCase;
use function BaclucC5Crud\Lib\collect as collect;

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