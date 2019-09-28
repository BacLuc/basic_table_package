<?php


namespace BasicTablePackage\Controller;


use Tightenco\Collect\Support\Collection;
use function BasicTablePackage\Lib\collect as collect;

class ActionRegistry
{
    /**
     * @var Collection
     */
    private $actions;

    /**
     * ActionRegistry constructor.
     * @param ActionProcessor[] $actions
     */
    public function __construct (array $actions)
    {
        $this->actions = collect($actions)->keyBy(function (ActionProcessor $item) { return $item->getName(); });
    }

    /**
     * @param String $name
     * @return ActionProcessor|null
     */
    public function getByName (String $name): ActionProcessor
    {
        return $this->actions->get($name);
    }


}