<?php

namespace Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged;

use BasicTablePackage\Adapters\Concrete5\Concrete5Renderer;
use BasicTablePackage\Adapters\Concrete5\Concrete5VariableSetter;
use BasicTablePackage\Controller\BasicTableController;
use BasicTablePackage\TableViewService;
use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    /**
     * @var null
     */
    private $obj;

    /**
     * Controller constructor.
     * @param null $obj
     */
    public function __construct ($obj = null)
    {
    }

    public function view(){
        $this->createBasicTableController()->view();
    }

    public function action_add_new_row_form ()
    {
        $this->createBasicTableController()->openForm(null);
    }

    /**
     * @return BasicTableController
     */
    private function createBasicTableController (): BasicTableController
    {
        return new BasicTableController(new Concrete5Renderer($this), new TableViewService(),
                                        new Concrete5VariableSetter($this), $this->obj);
    }

}