<?php


namespace BaclucC5Crud\Controller;


use BaclucC5Crud\View\ViewActionDefinition;

interface ActionConfiguration
{
    /**
     * @return ViewActionDefinition[]
     */
    public function getActions() : array;
}