<?php

namespace BaclucC5Crud\Controller;

use function BaclucC5Crud\Lib\collect as collect;
use Tightenco\Collect\Support\Collection;

class ActionRegistry {
    /**
     * @var Collection
     */
    private $actions;

    /**
     * ActionRegistry constructor.
     *
     * @param ActionProcessor[] $actions
     */
    public function __construct(array $actions) {
        $this->actions = collect($actions)->keyBy(function (ActionProcessor $item) {
            return $item->getName();
        });
    }

    /**
     * @return null|ActionProcessor
     */
    public function getByName(string $name): ActionProcessor {
        return $this->actions->get($name);
    }

    public function getActions() {
        return $this->actions->toArray();
    }
}
