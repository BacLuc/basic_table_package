<?php

namespace Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged;

use BasicTablePackage\Adapters\Concrete5\Concrete5Renderer;
use BasicTablePackage\Adapters\Concrete5\Concrete5VariableSetter;
use BasicTablePackage\Controller\BasicTableController;
use BasicTablePackage\TableViewService;
use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    private $basicTableController;

    /**
     * Controller constructor.
     * @param null $obj
     */
    public function __construct ($obj = null)
    {
        $this->basicTableController =
            new BasicTableController($obj, new Concrete5Renderer($this), new TableViewService(),
                                     new Concrete5VariableSetter($this));
    }

    public function action_add_new_row_form ()
    {
        $this->basicTableController->openForm(null);
    }

}