<?php

namespace BaclucC5Crud\Adapters\Concrete5;

use BaclucC5Crud\View\FormView\WysiwygEditor;
use BaclucC5Crud\View\FormView\WysiwygEditorFactory;
use Concrete\Core\Editor\EditorInterface;

class Concrete5WysiwygEditorFactory implements WysiwygEditorFactory {
    public function createEditor(): WysiwygEditor {
        return new Concrete5WysiwygEditor(\Core::make(EditorInterface::class));
    }
}
