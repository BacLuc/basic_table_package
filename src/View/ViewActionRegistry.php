<?php

namespace BaclucC5Crud\View;

use function BaclucC5Crud\Lib\collect as collect;
use Tightenco\Collect\Support\Collection;

class ViewActionRegistry {
    /**
     * @var Collection
     */
    private $actions;

    /**
     * ActionRegistry constructor.
     *
     * @param ViewActionDefinition[] $actions
     */
    public function __construct(array $actions) {
        $this->actions = collect($actions)->keyBy(function (ViewActionDefinition $item) {
            return $item->getAction();
        });
    }

    /**
     * @return null|ViewActionDefinition
     */
    public function getByName(string $name): ViewActionDefinition {
        return $this->actions->get($name);
    }

    public function getActions(): array {
        return $this->actions->toArray();
    }
}
