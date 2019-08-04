<?php /** @noinspection ALL */ ?>
<div class="table-responsive basic_table_package">
    <div class="tablecontrols">
        <?php foreach ($actions as $action) { ?>
            <?php /** @var $action \BasicTablePackage\View\Action */ ?>
            <form method="post" action="<?= $this->action($action->getAction()) ?>">
                <button type="submit" class="btn inlinebtn actionbutton <?= $action->getButtonClass() ?>"
                        aria-label="<?= t($action->getAriaLabel()) ?>"
                        title="<?= t($action->getTitle()) ?>">
                    <i class="fa <?= $action->getIconClass() ?>" aria-hidden="true"> </i>
                </button>
            </form>
        <?php } ?>
    </div>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <?php foreach ($headers as $value) { ?>
            <th><?= $value ?></th>
        <?php } ?>
        </thead>
        <tbody>
        <?php foreach ($rows as $row) { ?>
            <tr>
                <?php foreach ($row as $value) { ?>
                    <td><?= $value ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>