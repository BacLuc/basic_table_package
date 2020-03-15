<?php


namespace BaclucC5Crud\Controller;


class BlockIdSupplier
{
    /**
     * @var string
     */
    private $blockId;

    public function __construct($blockId)
    {
        $this->blockId = $blockId;
    }

    /**
     * @return string
     */
    public function getBlockId()
    {
        return $this->blockId;
    }
}