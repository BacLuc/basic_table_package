<?php

namespace BaclucC5Crud\Test\View\FormView;

use BaclucC5Crud\Entity\ValueSupplier;
use BaclucC5Crud\View\FormView\DropdownField;
use BaclucC5Crud\View\FormView\ValueTransformers\ValueTransformer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class DropdownFieldTest extends TestCase
{
    /**
     * @var ValueTransformer|MockObject
     */
    private $dropdownValueTransformerMock;
    /**
     * @var ValueTransformer|MockObject
     */
    private $identityValueTransformerMock;
    /**
     * @var ValueSupplier|MockObject
     */
    private $valueSupplier;
    /**
     * @var \Closure
     */
    private $dropdownFieldCallable;
    private $entity;

    protected function setUp()
    {
        $this->dropdownValueTransformerMock = $this->createMock(ValueTransformer::class);
        $this->identityValueTransformerMock = $this->createMock(ValueTransformer::class);
        $this->valueSupplier = $this->createMock(ValueSupplier::class);
        $fieldName = "test";
        $this->dropdownFieldCallable =
            DropdownField::createDropdownField($fieldName, $this->valueSupplier, $this->dropdownValueTransformerMock);
        $this->entity = new stdClass();
        $this->entity->{$fieldName} = null;
    }

    public function test_use_dropdownvaluetransformer_when_override_is_null()
    {
        $this->dropdownValueTransformerMock->expects($this->once())
                                           ->method("transform")
                                           ->willReturn(null);
        $this->identityValueTransformerMock->expects($this->never())
                                           ->method("transform");
        $fieldFactory = call_user_func($this->dropdownFieldCallable, null);
        $field = $fieldFactory($this->entity);

        $this->assertThat($field, $this->isInstanceOf(DropdownField::class));
    }

    public function test_use_override_when_override_is__not_null()
    {
        $this->identityValueTransformerMock->expects($this->once())
                                           ->method("transform")
                                           ->willReturn(null);
        $this->dropdownValueTransformerMock->expects($this->never())
                                           ->method("transform");
        $fieldFactory = call_user_func($this->dropdownFieldCallable, $this->identityValueTransformerMock);
        $field = $fieldFactory($this->entity);

        $this->assertThat($field, $this->isInstanceOf(DropdownField::class));
    }

}
