<?php

namespace BaclucC5Crud\Controller;

interface ActionProcessor {
    public function getName(): string;

    public function process(array $get, array $post, ...$additionalParameters);
}
