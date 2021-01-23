<?php

namespace BaclucC5Crud\Test\Adapters;

use BaclucC5Crud\Controller\Renderer;

class DefaultRenderer implements Renderer {
    /**
     * @var DefaultContext
     */
    private $defaultContext;

    /**
     * DefaultRenderer constructor.
     */
    public function __construct(DefaultContext $defaultContext) {
        $this->defaultContext = $defaultContext;
    }

    public function render(string $path) {
        require_once __DIR__.'/../../tests/Concrete5Functions.php';
        extract($this->defaultContext->getContext());

        include __DIR__.'/../../resources/'.$path.'.php';
    }

    public function action(string $action) {
        return $action;
    }
}
