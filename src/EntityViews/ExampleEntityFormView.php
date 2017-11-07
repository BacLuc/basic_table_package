<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 21.10.16
 * Time: 11:36
 */

namespace Concrete\Package\BasicTablePackage\Src\EntityViews;


use Concrete\Package\BasicTablePackage\Src\AbstractFormView;

class ExampleEntityFormView extends AbstractFormView
{
    /**
     * @param $form
     * @param $clientSideValidationActivated
     * @return mixed
     */
    public function getFormView($form, $clientSideValidationActivated = true)
    {
        $variables = $this->getFilledVariables($clientSideValidationActivated);

        $html = "
        <div class='row'>
            <div class='col-xs-12 col-sm-6 col-md-2'>
                <label>" . $variables['value']['label'] . "</label>
            
            </div>
            <div class='col-xs-12 col-sm-6 col-md-5'>
                " . $variables['value']['input'] . "
            
            </div>
        </div>
        
        ";
        return $html;
    }

}