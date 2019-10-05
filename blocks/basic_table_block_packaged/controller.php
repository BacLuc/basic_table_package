<?php

namespace Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged;

use BasicTablePackage\Adapters\Concrete5\DIContainerFactory;
use BasicTablePackage\Controller\ActionProcessor;
use BasicTablePackage\Controller\ActionRegistryFactory;
use BasicTablePackage\Controller\BasicTableController;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;
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
    public function view ()
    {
        $this->processAction($this->createBasicTableController()->getActionFor(ActionRegistryFactory::SHOW_TABLE));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_add_new_row_form ()
    {
        $this->processAction($this->createBasicTableController()
                                  ->getActionFor(ActionRegistryFactory::ADD_NEW_ROW_FORM));
    }

    /**
     * Attention: all action method are called twice.
     * Because this is a form submission, we stop after the function is executed
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_post_form ($blockId, $editId)
    {
        $this->processAction($this->createBasicTableController()->getActionFor(ActionRegistryFactory::POST_FORM));
        Redirect::page(Page::getCurrentPage())->send();
        exit();
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_cancel_form ()
    {
        $this->processAction($this->createBasicTableController()->getActionFor(ActionRegistryFactory::SHOW_TABLE));
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

    private function processAction (ActionProcessor $actionProcessor)
    {
        $actionProcessor->process($this->request->get(null) ? : [],
                                  $this->request->post(null) ? : []);
    }

}