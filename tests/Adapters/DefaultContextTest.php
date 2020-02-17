<?php

namespace BaclucC5Crud\Adapters;

use BaclucC5Crud\Test\Adapters\DefaultContext;
use PHPUnit\Framework\TestCase;

class DefaultContextTest extends TestCase
{
    const VAR_VALUE = "test";

    public function test_export_variables()
    {
        $defaultContext = new DefaultContext();
        $defaultContext->set("test", self::VAR_VALUE);
        extract($defaultContext->getContext());

        /** @noinspection PhpUndefinedVariableInspection */
        $this->assertThat($test, $this->equalTo(self::VAR_VALUE));
    }
}
