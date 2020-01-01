<?php

namespace BasicTablePackage\Test\Controller\Validation;

use BasicTablePackage\Controller\Validation\DropdownFieldValidator;
use BasicTablePackage\Entity\ValueSupplier;
use PHPUnit\Framework\TestCase;

class DropdownFieldValidatorTest extends TestCase
{
    private $valueSupplier;
    private $dropdownFieldValidator;

    function setUp()
    {
        $this->valueSupplier = $this->createMock(ValueSupplier::class);
        $this->dropdownFieldValidator = new DropdownFieldValidator("test", $this->valueSupplier);
    }

    public function test_fails_for_unknown_value()
    {
        $this->assertThat($this->dropdownFieldValidator->validate(["test" => "unkown"])->isError(), $this->isTrue());
    }

    public function test_return_valid_for_null()
    {
        $this->assertThat($this->dropdownFieldValidator->validate([])->isError(), $this->isFalse());
    }

    public function test_return_valid_for_empty_string()
    {
        $this->assertThat($this->dropdownFieldValidator->validate(["test" => ""])->isError(), $this->isFalse());
    }

    public function test_fail_for_unkown_0()
    {
        $this->assertThat($this->dropdownFieldValidator->validate(["test" => "0"])->isError(), $this->isTrue());
        $this->assertThat($this->dropdownFieldValidator->validate(["test" => 0])->isError(), $this->isTrue());
    }

    public function test_fail_for_empty_array()
    {
        $this->assertThat($this->dropdownFieldValidator->validate(["test" => []])->isError(), $this->isTrue());
    }

    public function test_return_valid_for_known_value()
    {
        $knownKey = "knownkey";
        $this->valueSupplier->expects($this->once())
                            ->method("getValues")
                            ->willReturn([$knownKey => "knownvalue"]);
        $this->assertThat($this->dropdownFieldValidator->validate(["test" => $knownKey])->isError(), $this->isFalse());
    }
}
