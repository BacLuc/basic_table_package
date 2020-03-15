<?php


namespace BaclucC5Crud\Controller;


class BlockIdSupplier
{
    /**
     * @var string
     */
    private $blockId;

    public function __construct(string $blockId)
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