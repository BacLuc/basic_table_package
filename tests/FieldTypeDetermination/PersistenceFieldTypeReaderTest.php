<?php


namespace BasicTablePackage\Test\FieldTypeDetermination;


use BasicTablePackage\FieldTypeDetermination\ColumnAnnotationHandler;
use BasicTablePackage\FieldTypeDetermination\ManyToOneAnnotationHandler;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypeReader;
use BasicTablePackage\FieldTypeDetermination\PersistenceFieldTypes;
use BasicTablePackage\Test\Entity\SomeEntity;
use PHPUnit\Framework\TestCase;

class PersistenceFieldTypeReaderTest extends TestCase
{

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function test_read_property_types()
    {
        $persistenceFieldTypeReader =
            new PersistenceFieldTypeReader(SomeEntity::class,
                [new ColumnAnnotationHandler(), new ManyToOneAnnotationHandler()]);
        self::assertThat($persistenceFieldTypeReader->getPersistenceFieldTypes(),
            self::equalTo([
                "value"          => PersistenceFieldTypes::STRING,
                "intcolumn"      => PersistenceFieldTypes::INTEGER,
                "datecolumn"     => PersistenceFieldTypes::DATE,
                "datetimecolumn" => PersistenceFieldTypes::DATETIME,
                "wysiwygcolumn"  => PersistenceFieldTypes::TEXT,
                "manyToOne"      => PersistenceFieldTypes::MANY_TO_ONE
            ]));
    }

}