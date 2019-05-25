<?php
/** @noinspection PhpParamsInspection */

namespace BasicTablePackage\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BasicTableControllerTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $renderer;

    protected function setUp ()
    {
        $this->renderer = $this->createMock(Renderer::class);
    }


    public function test_renders_table_view_when_created ()
    {
        $this->renderer->expects($this->once())->method('render')->with(BasicTableController::TABLE_VIEW);

        $this->createController();
    }

    public function test_renders_form_view_when_edit_action_called ()
    {
        $this->renderer->expects($this->exactly(2))->method('render')
                       ->withConsecutive([ $this->equalTo(BasicTableController::TABLE_VIEW) ],
                                         [ $this->equalTo(BasicTableController::FORM_VIEW) ])
        ;

        $basicTableController = $this->createController();

        $basicTableController->openForm(null);
    }

    /**
     * @return BasicTableController
     */
    protected function createController (): BasicTableController
    {
        $basicTableController = new BasicTableController(null, $this->renderer);
        return $basicTableController;
    }

}
