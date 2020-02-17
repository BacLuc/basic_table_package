<?php /** @noinspection ALL */ ?>
<div class="table-responsive bacluc_c5_crud">
    <div class="tablecontrols">
        <?php foreach ($actions as $action) { ?>
            <?php /** @var $action \BaclucC5Crud\View\ViewActionDefinition */ ?>
            <a href="<?= $this->action($action->getAction()) ?>">
                <button type="submit" class="btn inlinebtn actionbutton <?= $action->getButtonClass() ?>"
                        aria-label="<?= t($action->getAriaLabel()) ?>"
                        title="<?= t($action->getTitle()) ?>">
                    <i class="fa <?= $action->getIconClass() ?>" aria-hidden="true"> </i>
                </button>
            </a>
        <?php } ?>
    </div>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <th><?= t("Actions") ?></th>
        <?php foreach ($headers as $value) { ?>
            <th><?= $value ?></th>
        <?php } ?>
        </thead>
        <tbody>
        <?php
        foreach ($rows as $row) { ?>
            <tr>
                <td>
                    <?php foreach ($rowactions as $rowaction) { ?>
                        <a href="<?= $this->action($rowaction->getAction()) . "/" . $row->getId(); ?>">
                            <button type="submit" class="btn inlinebtn actionbutton <?= $rowaction->getButtonClass() ?>"
                                    aria-label="<?= t($rowaction->getAriaLabel()) ?>"
                                    title="<?= t($rowaction->getTitle()) ?>">
                                <i class="fa <?= $rowaction->getIconClass() ?>" aria-hidden="true"> </i>
                            </button>
                        </a>
                    <?php } ?>

                </td>
                <?php foreach ($row as $value) { ?>
                    <td><?= $value ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>