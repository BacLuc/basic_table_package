<?php /** @noinspection ALL */ ?>
<?php foreach ($properties as $name => $value) { ?>
    <?php /** @var $field \BaclucC5Crud\View\FormView\Field */ ?>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <label><?= t($name) ?></label>
        </div>
        <div class="col-xs-12 col-md-6"><?= $value ?></div>
    </div>
<?php } ?>
<div class="row">
    <div class="col-xs-6 col-sm-2">
        <a href="<?php echo $this->action('cancel_form') ?>">
            <button type="button" class="btn ccm-input-submit ccm-button btn-block">
                <?= t("back") ?>
            </button>
        </a>

    </div>
</div>