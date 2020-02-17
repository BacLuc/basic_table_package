<?php


namespace BaclucC5Crud\Controller;


interface ActionProcessor
{
    function getName(): string;

    function process(array $get, array $post, ...$additionalParameters);
}