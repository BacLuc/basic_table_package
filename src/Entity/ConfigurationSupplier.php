<?php

namespace BaclucC5Crud\Entity;

use BaclucC5Crud\Controller\BlockIdSupplier;

class ConfigurationSupplier {
    /**
     * @var ConfigurationRepository
     */
    private $configurationRepository;
    /**
     * @var BlockIdSupplier
     */
    private $blockIdSupplier;

    public function __construct(ConfigurationRepository $configurationRepository, BlockIdSupplier $blockIdSupplier) {
        $this->configurationRepository = $configurationRepository;
        $this->blockIdSupplier = $blockIdSupplier;
    }

    public function getConfiguration() {
        return $this->configurationRepository->getById($this->blockIdSupplier->getBlockId());
    }
}
