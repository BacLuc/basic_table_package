<?php


namespace BasicTablePackage\Test\Entity;


use BasicTablePackage\Entity\PersistenceFieldTypeReader;
use BasicTablePackage\Entity\PersistenceFieldTypes;
use PHPUnit\Framework\TestCase;

class PersistenceFieldTypeReaderTest extends TestCase
{
    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function test_read_property_types()
    {
        $persistenceFieldTypeReader = new PersistenceFieldTypeReader(SomeEntity::class);
        self::assertThat($persistenceFieldTypeReader->getPersistenceFieldTypes(),
            self::equalTo([
                "value"          => PersistenceFieldTypes::STRING,
                "intcolumn"      => PersistenceFieldTypes::INTEGER,
                "datecolumn"     => PersistenceFieldTypes::DATE,
                "datetimecolumn" => PersistenceFieldTypes::DATETIME,
            ]));
    }

}