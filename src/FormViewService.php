<?php


namespace BaclucC5Crud;


use BaclucC5Crud\Entity\Repository;
use BaclucC5Crud\View\FormType;
use BaclucC5Crud\View\FormView\FormView;
use BaclucC5Crud\View\FormView\FormViewFieldConfiguration;
use stdClass;
use function BaclucC5Crud\Lib\collect as collect;

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
    /**
     * @var FormType
     */
    private $formType;

    public function __construct(
        FormViewFieldConfiguration $formViewFieldConfiguration,
        Repository $repository,
        FormType $formType
    ) {
        $this->formViewFieldConfiguration = $formViewFieldConfiguration;
        $this->repository = $repository;
        $this->formType = $formType;
    }

    public function getFormView($editId = null): FormView
    {
        $entity = new stdClass();
        if ($editId != null) {
            $entity = $this->repository->getById($editId);
            if ($entity === null && $this->formType === FormType::$BLOCK_CONFIGURATION) {
                $entity = $this->repository->create();
            }
        }
        $fields =
            collect($this->formViewFieldConfiguration)->map(function ($fieldFactory) use ($entity) {
                return call_user_func($fieldFactory, $entity);
            });
        return new FormView($fields->toArray());
    }
}