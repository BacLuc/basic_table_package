<?php /** @noinspection ALL */ ?>
<div class="bacluc_c5_crud">
    <?php foreach ($properties as $name => $value) { ?>
        <?php /** @var \BaclucC5Crud\View\FormView\Field $field */ ?>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <label><?php echo t($name); ?></label>
            </div>
            <div class="col-xs-12 col-md-6"><?php echo $value; ?></div>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-xs-6 col-sm-2">
            <a href="<?php echo $this->action('cancel_form'); ?>">
                <button type="button" class="btn ccm-input-submit ccm-button btn-block">
                    <?php echo t('back'); ?>
                </button>
            </a>

        </div>
    </div>
</div>