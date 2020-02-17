<?php


namespace BaclucC5Crud;


use BaclucC5Crud\Controller\Validation\ValidationResult;
use BaclucC5Crud\Controller\Validation\ValidationResultItem;
use BaclucC5Crud\View\FormView\FormView;
use BaclucC5Crud\View\FormView\PostFormViewConfigurationFactory;
use Error;
use stdClass;
use function BaclucC5Crud\Lib\collect as collect;

class FormViewAfterValidationFailedService
{
    /**
     * @var PostFormViewConfigurationFactory
     */
    private $postFormViewConfigurationFactory;

    /**
     * ShowFormActionProcessor constructor.
     * @param PostFormViewConfigurationFactory $postFormViewConfigurationFactory
     */
    public function __construct(
        PostFormViewConfigurationFactory $postFormViewConfigurationFactory
    ) {
        $this->postFormViewConfigurationFactory = $postFormViewConfigurationFactory;
    }

    function getFormView(ValidationResult $validationResult)
    {
        $entity = new stdClass();
        /**
         * @var ValidationResultItem $validationResultItem
         */
        foreach ($validationResult as $key => $validationResultItem) {
            try {
                $entity->{$key} = $validationResultItem->getPostValue();
            } catch (Error $ignored) {
            }
        }
        $fields =
            collect($this->postFormViewConfigurationFactory->createConfiguration())->map(function ($fieldFactory) use (
                $entity
            ) {
                return call_user_func($fieldFactory, $entity);
            });
        return new FormView($fields->toArray());
    }

}