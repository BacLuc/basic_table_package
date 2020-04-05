<?php /** @noinspection ALL */ ?>
<?php if ($addFormTags) { ?>
    <form action="<?php echo $this->action($submitFormAction->getAction()) .
                             (isset($editId) && $editId != null ? "/$editId" :
                                 "") ?>" method='POST' enctype="multipart/form-data" novalidate>
<?php } ?>
<?php foreach ($fields as $name => $field) { ?>
    <?php /** @var $field \BaclucC5Crud\View\FormView\Field */ ?>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <label><?= t($field->getLabel()) ?></label>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="row">
                <div class="col-xs-12">
                    <?= $field->getFormView() ?>
                </div>
                <div class="col-xs-12">
                    <?php $validationErrors = isset($validationErrors) ? $validationErrors : null; ?>
                    <?php if ($validationErrors) { ?>
                        <ul>
                            <?php foreach ($validationErrors[$name] as $fieldValidationError) { ?>
                                <li><?= $fieldValidationError ?></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($addFormTags) { ?>
    <div class="row">
        <div class="col-xs-6 col-sm-2">
            <button type="submit" class="btn ccm-input-submit btn-primary btn-block" name="submit"
                    value="save"><?= t("save") ?></button>
        </div>
        <div class="col-xs-6 col-sm-2">
            <a href="<?php echo $this->action($cancelFormAction->getAction()) ?>">
                <button type="button" class="btn ccm-input-submit ccm-button btn-block">
                    <?= t("cancel") ?>
                </button>
            </a>

        </div>
    </div>
    </form>
<?php } ?>