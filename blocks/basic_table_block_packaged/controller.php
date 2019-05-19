<?php

namespace Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged;

use BasicTablePackage\Adapters\Concrete5\Concrete5Renderer;
use BasicTablePackage\Controller\BasicTableController;
use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{

    /**
     * Controller constructor.
     * @param null $obj
     */
    public function __construct ($obj = null) {
        new BasicTableController($obj, new Concrete5Renderer($this));
    }

}