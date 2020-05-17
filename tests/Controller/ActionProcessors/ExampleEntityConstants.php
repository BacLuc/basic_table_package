<?php


namespace BaclucC5Crud\Test\Controller\ActionProcessors;


use BaclucC5Crud\Entity\ExampleEntityDropdownValueSupplier;
use BaclucC5Crud\Entity\ReferencedEntity;
use BaclucC5Crud\Entity\RepositoryFactory;
use DateTime;
use DI\Container;
use function BaclucC5Crud\Lib\collect as collect;

class ExampleEntityConstants
{

    public const INT_VAL_1              = 65498;
    public const TEXT_VAL_1             = "test_value";
    public const DATE_VALUE_1           = '12.12.2020';
    const        DATETIME_VALUE_1       = '12.12.2020 12:59';
    const        WYSIWYG_VALUE_1        = BigTestValues::WYSIWYGVALUE;
    const        DROPDOWN_KEY_5         = ExampleEntityDropdownValueSupplier::KEY_5;
    const        DROPDOWN_VALUE_5       = ExampleEntityDropdownValueSupplier::VALUE_5;
    const        REFERENCED_ENTITY_ID_1 = 1;
    const        REFERENCED_ENTITY_ID_2 = 2;
    public const ENTRY_1_POST           = [
        "value"          => self::TEXT_VAL_1,
        "intcolumn"      => self::INT_VAL_1,
        "datecolumn"     => self::DATE_VALUE_1,
        "datetimecolumn" => self::DATETIME_VALUE_1,
        "wysiwygcolumn"  => self::WYSIWYG_VALUE_1,
        "dropdowncolumn" => self::DROPDOWN_KEY_5,
        "manyToOne"      => self::REFERENCED_ENTITY_ID_1,
        "manyToMany"     => [self::REFERENCED_ENTITY_ID_1, self::REFERENCED_ENTITY_ID_2]
    ];

    public static function getFormValues()
    {
        $values = self::ENTRY_1_POST;
        $values['datecolumn'] = DateTime::createFromFormat("d.m.Y", $values['datecolumn'])->format("Y-m-d");
        $dateTime = DateTime::createFromFormat("d.m.Y H:i:s", $values['datetimecolumn']);
        if (!$dateTime) {
            $dateTime = DateTime::createFromFormat("d.m.Y H:i", $values['datetimecolumn']);
        }
        $values['datetimecolumn'] = $dateTime->format("Y-m-d H:i");
        return $values;
    }

    public static function getValues()
    {
        $postValues = self::ENTRY_1_POST;
        $exampleEntityDropdownValueSupplier = new ExampleEntityDropdownValueSupplier();
        $dropDownValues = $exampleEntityDropdownValueSupplier->getValues();
        $postValues["dropdowncolumn"] = $dropDownValues[self::ENTRY_1_POST["dropdowncolumn"]];
        $referencedEntityValues = self::getReferencedEntityValues();
        $postValues["manyToOne"] = $referencedEntityValues[self::ENTRY_1_POST["manyToOne"]];
        $tableManyToMany = collect($postValues["manyToMany"])
            ->map(function ($id) use ($referencedEntityValues) {
                return $referencedEntityValues[$id];
            })
            ->map(function (ReferencedEntity $entity) {
                return $entity->createUniqueString();
            })
            ->join(",");
        $postValues["manyToMany"] = $tableManyToMany;
        return $postValues;
    }

    /**
     * @var ReferencedEntity
     */
    private static $MANY_TO_ONE_VALUE_1;

    public static function getReferencedEntity1()
    {
        if (static::$MANY_TO_ONE_VALUE_1 == null) {
            static::$MANY_TO_ONE_VALUE_1 = new ReferencedEntity();
            static::$MANY_TO_ONE_VALUE_1->id = self::REFERENCED_ENTITY_ID_1;
            static::$MANY_TO_ONE_VALUE_1->value = "referenced1";
            static::$MANY_TO_ONE_VALUE_1->intcolumn = self::REFERENCED_ENTITY_ID_1;
        }
        return static::$MANY_TO_ONE_VALUE_1;
    }

    /**
     * @var ReferencedEntity
     */
    private static $MANY_TO_ONE_VALUE_2;

    public static function getReferencedEntity2()
    {
        if (static::$MANY_TO_ONE_VALUE_2 == null) {
            static::$MANY_TO_ONE_VALUE_2 = new ReferencedEntity();
            static::$MANY_TO_ONE_VALUE_2->id = self::REFERENCED_ENTITY_ID_2;
            static::$MANY_TO_ONE_VALUE_2->value = "referenced2";
            static::$MANY_TO_ONE_VALUE_2->intcolumn = self::REFERENCED_ENTITY_ID_2;
        }
        return static::$MANY_TO_ONE_VALUE_2;
    }

    /**
     * @param Container $container
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public static function addReferencedEntityTestValues(Container $container): void
    {
        $referencedEntityRepository = $container->get(RepositoryFactory::class)
                                                ->createRepositoryFor(ReferencedEntity::class);

        $referencedEntityRepository->persist(ExampleEntityConstants::getReferencedEntity1());
        $referencedEntityRepository->persist(ExampleEntityConstants::getReferencedEntity2());
    }

    /**
     * @return array
     */
    public static function getReferencedEntityValues(): array
    {
        return [
            self::REFERENCED_ENTITY_ID_1 => self::getReferencedEntity1(),
            self::REFERENCED_ENTITY_ID_2 => self::getReferencedEntity2()
        ];
    }
}