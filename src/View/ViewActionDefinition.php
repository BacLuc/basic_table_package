<?php


namespace BaclucC5Crud\View;


class ViewActionDefinition
{
    /**
     * @var string
     */
    private $action;
    /**
     * @var string
     */
    private $buttonClass;
    /**
     * @var string
     */
    private $ariaLabel;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $iconClass;


    /**
     * Action constructor.
     */
    public function __construct(
        string $action,
        string $buttonClass,
        string $ariaLabel,
        string $title,
        string $iconClass
    ) {
        $this->action = $action;
        $this->buttonClass = $buttonClass;
        $this->ariaLabel = $ariaLabel;
        $this->title = $title;
        $this->iconClass = $iconClass;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getButtonClass()
    {
        return $this->buttonClass;
    }

    /**
     * @return string
     */
    public function getAriaLabel()
    {
        return $this->ariaLabel;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getIconClass()
    {
        return $this->iconClass;
    }
}