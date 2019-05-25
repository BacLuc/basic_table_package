<?php


namespace BasicTablePackage\Adapters;


use BasicTablePackage\Controller\Renderer;

class DefaultRenderer implements Renderer
{
    /**
     * @var DefaultContext
     */
    private $defaultContext;


    /**
     * DefaultRenderer constructor.
     * @param DefaultContext $defaultContext
     */
    public function __construct (DefaultContext $defaultContext) {
        $this->defaultContext = $defaultContext;
    }

    public function render (string $path)
    {
        extract($this->defaultContext->getContext());
        include __DIR__.'/../'.$path.'.php';
    }
}