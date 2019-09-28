<?php


namespace BasicTablePackage\View;


use Tightenco\Collect\Support\Collection;
use function BasicTablePackage\Lib\collect as collect;

class ViewActionRegistry
{
    /**
     * @var Collection
     */
    private $actions;

    /**
     * ActionRegistry constructor.
     * @param ViewActionDefinition[] $actions
     */
    public function __construct (array $actions)
    {
        $this->actions = collect($actions)->keyBy(function (ViewActionDefinition $item) { return $item->getAction(); });
    }

    /**
     * @param String $name
     * @return ViewActionDefinition|null
     */
    public function getByName (String $name): ViewActionDefinition
    {
        return $this->actions->get($name);
    }


}