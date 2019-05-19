<?php

namespace BasicTablePackage\Controller;

use PHPUnit\Framework\TestCase;

class BasicTableControllerTest extends TestCase
{
    public function testInstantiate(){
        $basicTableController= new BasicTableController();
        self::assertNotNull($basicTableController);
    }

}
