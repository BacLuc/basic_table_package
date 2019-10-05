<?php /** @noinspection ALL */ ?>
<form action=<?php echo $this->action('post_form') . (isset($editId) && $editId != null ? "/$editId" :
        "") ?> method='POST'>
    <?php foreach ($fields as $field) { ?>
        <?php /** @var $field \BasicTablePackage\View\FormView\Field */ ?>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <label><?= t($field->getLabel()) ?></label>
            </div>
            <div class="col-xs-12 col-md-6"><?= $field->getFormView() ?></div>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-xs-6 col-sm-2">
            <button type="submit" class="btn ccm-input-submit btn-primary btn-block" name="submit"
                    value="save"><?= t("save") ?></button>
        </div>
        <div class="col-xs-6 col-sm-2">
            <a href="<?php echo $this->action('cancel_form') ?>">
                <button type="button" class="btn ccm-input-submit ccm-button btn-block">
                    <?= t("cancel") ?>
                </button>
            </a>

        </div>
    </div>
</form>