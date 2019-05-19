<?php

namespace BasicTablePackage\Controller;

use PHPUnit\Framework\TestCase;

class BasicTableControllerTest extends TestCase
{
    /**
     * @var Renderer
     */
    private $renderer;

    protected function setUp ()
    {
        $this->renderer = $this->createMock(Renderer::class);
    }


    public function test_renders_table_view_when_created(){
        $this->renderer->expects($this->once())->method('render')->with(BasicTableController::TABLE_VIEW);

        new BasicTableController(null, $this->renderer);
    }

}
