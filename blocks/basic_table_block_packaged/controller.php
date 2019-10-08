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
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_edit_row_form ($ignored, $editId)
    {
        $this->processAction($this->createBasicTableController()
                                  ->getActionFor(ActionRegistryFactory::EDIT_ROW_FORM),
                             $editId);
    }

    /**
     * Attention: all action method are called twice.
     * Because this is a form submission, we stop after the function is executed
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_post_form ($ignored, $editId = null)
    {
        $this->processAction($this->createBasicTableController()->getActionFor(ActionRegistryFactory::POST_FORM),
                             $editId);
        Redirect::page(Page::getCurrentPage())->send();
        exit();
    }

    /**
     * @param $ignored
     * @param $toDeleteId
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_delete_entry ($ignored, $toDeleteId)
    {
        $this->processAction($this->createBasicTableController()->getActionFor(ActionRegistryFactory::DELETE_ENTRY),
                             $toDeleteId);
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
     * @param $ignored
     * @param $toShowId
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_show_details($ignored, $toShowId)
    {
        $this->processAction($this->createBasicTableController()->getActionFor(ActionRegistryFactory::SHOW_ENTRY_DETAILS), $toShowId);
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

    private function processAction (ActionProcessor $actionProcessor, ...$additionalParams)
    {
        $actionProcessor->process($this->request->get(null) ? : [],
                                  $this->request->post(null) ? : [],
                                  array_key_exists(0, $additionalParams) ? $additionalParams[0] : null);
    }

}