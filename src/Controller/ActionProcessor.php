<?php


namespace BasicTablePackage\Controller;


interface ActionProcessor
{
    function getName (): string;

    function process (array $get, array $post);
}