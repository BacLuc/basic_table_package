<?php /** @noinspection ALL */
$maxPageNum = floor($count / $pageSize);
?>
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
    <div class="tablecount">
        <span class="count-title"><?= t('Total entries:') ?></span><span><?= $count ?></span>
    </div>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <?php if ($rowactions) { ?>
            <th><?= t("Actions") ?></th>
        <?php } ?>
        <?php foreach ($headers as $value) { ?>
            <th><?= $value ?></th>
        <?php } ?>
        </thead>
        <tbody>
        <?php
        foreach ($rows as $row) { ?>
            <tr>
                <?php if ($rowactions) { ?>
                    <td>
                        <?php foreach ($rowactions as $rowaction) { ?>
                            <a href="<?= $this->action($rowaction->getAction()) . "/" . $row->getId(); ?>">
                                <button type="submit"
                                        class="btn inlinebtn actionbutton <?= $rowaction->getButtonClass() ?>"
                                        aria-label="<?= t($rowaction->getAriaLabel()) ?>"
                                        title="<?= t($rowaction->getTitle()) ?>">
                                    <i class="fa <?= $rowaction->getIconClass() ?>" aria-hidden="true"> </i>
                                </button>
                            </a>
                        <?php } ?>

                    </td>
                <?php } ?>
                <?php foreach ($row as $value) { ?>
                    <td><?= $value ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <div>
        <div class="col-xs-12 col-sm-6">
            <ul class="pagination">
                <li class="page-item <?= $currentPage == 0 ? "disabled" : "" ?>">
                    <a href="<?= $currentURL . "?pageSize=$pageSize&currentPage=0" ?>"
                       aria-label="<?= t('First') ?>">
                        <span aria-hidden="true">«</span>
                    </a>
                </li>
                <li class="page-item <?= $currentPage == 0 ? "disabled" : "" ?>">
                    <a href="<?= $currentURL . "?pageSize=$pageSize&currentPage=" . ($currentPage - 1) ?>"
                       aria-label="<?= t('Previous') ?>">
                        <span aria-hidden="true">&lt;</span>
                    </a>
                </li>
                <?php for ($i = max(0, $currentPage - 3); $i <= min(($currentPage + 3), $maxPageNum); $i++) { ?>

                    <li class="page-item <?= $currentPage == $i ? "disabled" : "" ?>"><a
                                class="page-link <?= $i == $currentPage ? "active" : "" ?>"
                                href="<?= $currentURL .
                                          "?pageSize=$pageSize&currentPage=$i" ?>">
                            <?= $i + 1 ?></a>
                    </li>
                <?php } ?>
                <li class="page-item <?= $currentPage == $maxPageNum ? "disabled" : "" ?>">
                    <a href="<?= $currentURL .
                                 "?pageSize=$pageSize&currentPage=" .
                                 ($currentPage +
                                  1) ?>"
                       aria-label="<?= t('Next') ?>">
                        <span aria-hidden="true">&gt;</span>
                    </a>
                </li>
                <li class="page-item<?= $currentPage == $maxPageNum ? "disabled" : "" ?>">
                    <a href="<?= $currentURL .
                                 "?pageSize=$pageSize&currentPage=$maxPageNum" ?>"
                       aria-label="<?= t('Last') ?>">
                        <span aria-hidden="true">»</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-xs-12 col-sm-6">
            <form method="get" action="<?= $currentURL ?>">
                <div>
                    <div class="col-xs-12 col-md-5">
                        <label><?= t($pageSizeField->getLabel()) ?></label>
                    </div>
                    <div class="col-xs-12 col-md-5">
                        <?= $pageSizeField->getFormView() ?>
                    </div>
                    <div class="col-xs-12 col-md-2">
                        <button type="submit"
                                class="btn inlinebtn?>"
                                aria-label="<?= t("Go") ?>"
                                title="<?= t("Go") ?>">
                            <?= t("Go") ?>
                        </button>
                    </div>
                    <input type="hidden" name="currentPage" value="<?= $currentPage ?>">
                </div>
            </form>
        </div>
    </div>

</div>