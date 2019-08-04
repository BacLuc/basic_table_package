<?php /** @noinspection ALL */ ?>
<?php foreach ($fields as $field) { ?>
    <?php /** @var $field \BasicTablePackage\View\FormView\Field */ ?>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <label><?= t($field->getLabel()) ?></label>
        </div>
        <div class="col-xs-12 col-md-6"><?= $field->getFormView() ?></div>
    </div>
<?php } ?>
