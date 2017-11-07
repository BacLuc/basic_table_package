<div class="form-group basic_table_package">

    <form action=<?php echo $this->action('save_row') ?> method='POST' class="
	<?php echo $controller->isClientSideValidationActivated() ? 'bacluc-client-side-validation' : ''; ?>
	">


        <?php
        //print consistency error messages

        $consistencyErrors = $controller->getConsistencyErrors();
        if (count($consistencyErrors) > 0) {
            foreach ($consistencyErrors as $error) {
                print("
			<div class='alert alert-danger'>$error</div>
			");
            }
        }


        /**
         * @var \Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged\Controller $controller
         */
        $model = $controller->getModel();

        /**
         * @var \Concrete\Package\BasicTablePackage\Src\AbstractFormView $defaultView
         */
        $defaultView = $model->getDefaultFormView($form, $controller->isClientSideValidationActivated());

        if ($defaultView === false) {
            $fields = $controller->getFields();
            //$rowValues = $controller->getRowValues();
            $errorFields = $controller->getErrorFields();
            /**
             * @var \Concrete\Package\BasicTablePackage\Src\FieldTypes\Field $FieldObject
             */
            foreach ($fields as $field => $FieldObject) {
                if ($field == 'id') {

                } else {
                    if ($FieldObject->showInForm()) {

                        $FieldObject->setSQLValue($model->get($FieldObject->getSQLFieldName()));

                        if (isset($errorFields[$FieldObject->getPostName()])) {
                            $FieldObject->setErrorMessage($errorFields[$FieldObject->getPostName()]->getErrorMsg());
                        }
                        $FieldObject->setView($this);
                        echo $FieldObject->getFormView($form, $controller->isClientSideValidationActivated());
                    }

                }

            }
        } else {
            $defaultView->setErrorMsg($errorFields);
            print($defaultView->getFormView($form, $controller->isClientSideValidationActivated()));
        }


        echo $form->submit('submit', t("save"));
        echo $form->submit('cancel', t("cancel"));
        ?>


    </form>

</div>

