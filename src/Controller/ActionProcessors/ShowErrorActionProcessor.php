<?php

namespace BaclucC5Crud\Controller\ActionProcessors;

use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\Renderer;

class ShowErrorActionProcessor implements ActionProcessor {
    /**
     * @var Renderer
     */
    private $renderer;

    public function __construct(Renderer $renderer) {
        $this->renderer = $renderer;
    }

    public function getName(): string {
        return ActionRegistryFactory::SHOW_ERROR;
    }

    public function process(array $get, array $post, ...$additionalParameters) {
        $this->renderer->render('view/error');
    }
}
