<?php

namespace BaclucC5Crud\Test\Controller\Validation;

use BaclucC5Crud\Controller\Validation\SelectMultipleFieldValidator;
use BaclucC5Crud\Entity\ValueSupplier;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class SelectMultipleFieldValidatorTest extends TestCase {
    private $valueSupplier;
    private $dropdownFieldValidator;

    public function setUp() {
        $this->valueSupplier = $this->createMock(ValueSupplier::class);
        $this->dropdownFieldValidator = new SelectMultipleFieldValidator('test', $this->valueSupplier);
    }

    public function testFailsForUnknownValue() {
        $this->assertThat($this->dropdownFieldValidator->validate(['test' => 'unkown'])->isError(), $this->isTrue());
    }

    public function testReturnValidForNull() {
        $this->assertThat($this->dropdownFieldValidator->validate([])->isError(), $this->isFalse());
    }

    public function testReturnValidForEmptyString() {
        $this->assertThat($this->dropdownFieldValidator->validate(['test' => ''])->isError(), $this->isFalse());
    }

    public function testFailForUnkown0() {
        $this->assertThat($this->dropdownFieldValidator->validate(['test' => '0'])->isError(), $this->isTrue());
        $this->assertThat($this->dropdownFieldValidator->validate(['test' => 0])->isError(), $this->isTrue());
    }

    public function testReturnValidForEmptyArray() {
        $this->assertThat($this->dropdownFieldValidator->validate(['test' => []])->isError(), $this->isFalse());
    }

    public function testReturnValidForKnownValue() {
        $knownKey = 'knownkey';
        $this->valueSupplier->expects($this->once())
            ->method('getValues')
            ->willReturn([$knownKey => 'knownvalue'])
        ;
        $this->assertThat(
            $this->dropdownFieldValidator->validate(['test' => [$knownKey]])->isError(),
            $this->isFalse()
        );
    }

    public function testReturnValidForKnownValues() {
        $knownKey = 'knownkey';
        $knownKey2 = 'knownkey2';
        $this->valueSupplier->expects($this->once())
            ->method('getValues')
            ->willReturn([$knownKey => 'knownvalue', $knownKey2 => 'knownvalue2'])
        ;
        $this->assertThat(
            $this->dropdownFieldValidator->validate(['test' => [$knownKey, $knownKey2]])->isError(),
            $this->isFalse()
        );
    }
}
