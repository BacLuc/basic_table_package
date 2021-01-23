<?php

namespace BaclucC5Crud\View\FormView;

interface Field {
    public function getFormView(): string;

    public function getLabel(): string;
}
