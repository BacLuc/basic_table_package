<?php


namespace BaclucC5Crud\Adapters\Concrete5;


use BaclucC5Crud\Controller\CurrentUrlSupplier;
use Concrete\Core\Block\BlockController;

class Concrete5CurrentUrlSupplier implements CurrentUrlSupplier
{
    /**
     * @var BlockController
     */
    private $blockController;


    public function __construct(BlockController $blockController)
    {
        $this->blockController = $blockController;
    }

    function getUrl()
    {
        return $this->blockController->getRequest()
                                     ->getPathInfo();
    }
}