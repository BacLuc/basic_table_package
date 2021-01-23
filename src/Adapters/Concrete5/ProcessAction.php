<?php

namespace BaclucC5Crud\Adapters\Concrete5;

use BaclucC5Crud\Controller\ActionProcessor;
use Concrete\Core\Block\BlockController;

class ProcessAction {
    public static function processAction(
        BlockController $blockController,
        ActionProcessor $actionProcessor,
        ...$additionalParams
    ) {
        return $actionProcessor->process(
            $blockController->getRequest()->query->all() ?: [],
            $blockController->getRequest()->post(null) ?: [],
            array_key_exists(0, $additionalParams) ? $additionalParams[0] : null
        );
    }
}
