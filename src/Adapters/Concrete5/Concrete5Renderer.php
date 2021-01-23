<?php

namespace BaclucC5Crud\Adapters\Concrete5;

use BaclucC5Crud\Controller\Renderer;
use BadMethodCallException;
use Concrete\Core\Block\BlockController;
use Concrete\Package\BaclucC5Crud\Controller;

class Concrete5Renderer implements Renderer {
    /**
     * @var BlockController
     */
    private $blockController;
    /**
     * @var string
     */
    private $packagePath;
    private $crudPackagePath;

    public function __construct(BlockController $blockController, string $packagePath) {
        $this->blockController = $blockController;
        $this->packagePath = $packagePath;
    }

    public function render(string $path) {
        if (file_exists($this->packagePath.'/resources/'.$path.'.php')) {
            $packageHandle = basename($this->packagePath);
            $this->blockController->render('../../../'.$packageHandle.'/resources/'.$path);
        } else {
            $this->blockController->render('../../../'.Controller::PACKAGE_HANDLE.'/resources/'.$path);
        }
    }

    public function action(string $action) {
        throw new BadMethodCallException('this method is not implemented');
    }
}
