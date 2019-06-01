<?php

namespace Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged;

use BasicTablePackage\Adapters\Concrete5\DIContainerFactory;
use BasicTablePackage\Controller\BasicTableController;
use Concrete\Core\Block\BlockController;
use Concrete\Package\BasicTablePackage\Controller as PackageController;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;

class Controller extends BlockController
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function view(){
        $this->createBasicTableController()->view();
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_add_new_row_form ()
    {
        $this->createBasicTableController()->openForm(null);
    }

    /**
     * @return BasicTableController
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    private function createBasicTableController (): BasicTableController
    {
        $entityManager = PackageController::getEntityManagerStatic();
        $container = DIContainerFactory::createContainer($this, $entityManager);
        return $container->get(BasicTableController::class);
    }

}