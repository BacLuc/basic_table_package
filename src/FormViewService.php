<?php


namespace BasicTablePackage;


use BasicTablePackage\Entity\Repository;
use BasicTablePackage\View\FormView\FormView;
use BasicTablePackage\View\FormView\FormViewFieldConfiguration;
use stdClass;
use function BasicTablePackage\Lib\collect as collect;

class FormViewService
{
    /**
     * @var FormViewFieldConfiguration
     */
    private $formViewFieldConfiguration;
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(FormViewFieldConfiguration $formViewFieldConfiguration, Repository $repository)
    {
        $this->formViewFieldConfiguration = $formViewFieldConfiguration;
        $this->repository = $repository;
    }

    public function getFormView($editId = null): FormView
    {
        $entity = new stdClass();
        if ($editId != null) {
            $entity = $this->repository->getById($editId);
        }
        $fields =
            collect($this->formViewFieldConfiguration)->map(function ($fieldFactory) use ($entity) {
                return call_user_func($fieldFactory, $entity);
            });
        return new FormView($fields->toArray());
    }
}