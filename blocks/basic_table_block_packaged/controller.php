<?php

namespace Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged;

use BasicTablePackage\Controller\BasicTableController;
use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{

    /**
     * Controller constructor.
     * @param null $obj
     */
    public function __construct ($obj = null) {
        new BasicTableController($obj);
    }

}