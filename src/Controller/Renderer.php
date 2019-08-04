<?php


namespace BasicTablePackage\Controller;


interface Renderer
{
    public function render (string $path);

    public function action (string $action);
}