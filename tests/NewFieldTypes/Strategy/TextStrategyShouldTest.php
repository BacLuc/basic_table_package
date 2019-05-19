<?php

namespace NewFieldTypes\Strategy;

use Concrete\Package\BasicTablePackage\Src\NewFieldTypes\FieldType;
use Concrete\Package\BasicTablePackage\Src\NewFieldTypes\Strategy\TextStrategy;
use PHPUnit\Framework\TestCase;

if(!function_exists("t")){
    function t(){}
}

class TextStrategyShouldTest extends TestCase
{
    private $fieldType;

    protected function setUp ()
    {
        $this->fieldType = $this->createMock(FieldType::class);
    }

    public function test_return_false_if_value_is_null(){
        $this->fieldType->method("getNullable")->willReturn(false);

        $textStrategy = new TestTextStrategy($this->fieldType);

        $this->assertFalse($textStrategy->validatePost(null));
    }

}

class TestTextStrategy extends TextStrategy {
    protected function t ($text): string { return "";}
}
