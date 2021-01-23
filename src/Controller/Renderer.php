<?php

namespace BaclucC5Crud\Controller;

interface Renderer {
    public function render(string $path);

    public function action(string $action);
}
