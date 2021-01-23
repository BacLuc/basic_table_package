<?php /** @noinspection ALL */
$maxPageNum = floor($count / $pageSize);
?>
<div class="table-responsive bacluc_c5_crud">
    <div class="tablecontrols">
        <?php foreach ($actions as $action) { ?>
            <?php /** @var \BaclucC5Crud\View\ViewActionDefinition $action */ ?>
            <a href="<?php echo $this->action($action->getAction()); ?>">
                <button type="submit" class="btn inlinebtn actionbutton <?php echo $action->getButtonClass(); ?>"
                        aria-label="<?php echo t($action->getAriaLabel()); ?>"
                        title="<?php echo t($action->getTitle()); ?>">
                    <i class="fa <?php echo $action->getIconClass(); ?>" aria-hidden="true"> </i>
                </button>
            </a>
        <?php } ?>
    </div>
    <div class="tablecount">
        <span class="count-title"><?php echo t('Total entries:'); ?></span><span><?php echo $count; ?></span>
    </div>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <?php if ($rowactions) { ?>
            <th><?php echo t('Actions'); ?></th>
        <?php } ?>
        <?php foreach ($headers as $value) { ?>
            <th><?php echo $value; ?></th>
        <?php } ?>
        </thead>
        <tbody>
        <?php
        foreach ($rows as $row) { ?>
            <tr>
                <?php if ($rowactions) { ?>
                    <td>
                        <?php foreach ($rowactions as $rowaction) { ?>
                            <a href="<?php echo $this->action($rowaction->getAction()).'/'.$row->getId(); ?>">
                                <button type="submit"
                                        class="btn inlinebtn actionbutton <?php echo $rowaction->getButtonClass(); ?>"
                                        aria-label="<?php echo t($rowaction->getAriaLabel()); ?>"
                                        title="<?php echo t($rowaction->getTitle()); ?>">
                                    <i class="fa <?php echo $rowaction->getIconClass(); ?>" aria-hidden="true"> </i>
                                </button>
                            </a>
                        <?php } ?>

                    </td>
                <?php } ?>
                <?php foreach ($row as $value) { ?>
                    <td><?php echo $value; ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <div>
        <div class="col-xs-12 col-sm-6">
            <ul class="pagination">
                <li class="page-item <?php echo 0 == $currentPage ? 'disabled' : ''; ?>">
                    <a href="<?php echo $currentURL."?pageSize={$pageSize}&currentPage=0"; ?>"
                       aria-label="<?php echo t('First'); ?>">
                        <span aria-hidden="true">«</span>
                    </a>
                </li>
                <li class="page-item <?php echo 0 == $currentPage ? 'disabled' : ''; ?>">
                    <a href="<?php echo $currentURL."?pageSize={$pageSize}&currentPage=".($currentPage - 1); ?>"
                       aria-label="<?php echo t('Previous'); ?>">
                        <span aria-hidden="true">&lt;</span>
                    </a>
                </li>
                <?php for ($i = max(0, $currentPage - 3); $i <= min(($currentPage + 3), $maxPageNum); ++$i) { ?>

                    <li class="page-item <?php echo $currentPage == $i ? 'disabled' : ''; ?>"><a
                                class="page-link <?php echo $i == $currentPage ? 'active' : ''; ?>"
                                href="<?php echo $currentURL.
                                          "?pageSize={$pageSize}&currentPage={$i}"; ?>">
                            <?php echo $i + 1; ?></a>
                    </li>
                <?php } ?>
                <li class="page-item <?php echo $currentPage == $maxPageNum ? 'disabled' : ''; ?>">
                    <a href="<?php echo $currentURL.
                                 "?pageSize={$pageSize}&currentPage=".
                                 ($currentPage +
                                  1); ?>"
                       aria-label="<?php echo t('Next'); ?>">
                        <span aria-hidden="true">&gt;</span>
                    </a>
                </li>
                <li class="page-item<?php echo $currentPage == $maxPageNum ? 'disabled' : ''; ?>">
                    <a href="<?php echo $currentURL.
                                 "?pageSize={$pageSize}&currentPage={$maxPageNum}"; ?>"
                       aria-label="<?php echo t('Last'); ?>">
                        <span aria-hidden="true">»</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-xs-12 col-sm-6">
            <form method="get" action="<?php echo $currentURL; ?>">
                <div>
                    <div class="col-xs-12 col-md-5">
                        <label><?php echo t($pageSizeField->getLabel()); ?></label>
                    </div>
                    <div class="col-xs-12 col-md-5">
                        <?php echo $pageSizeField->getFormView(); ?>
                    </div>
                    <div class="col-xs-12 col-md-2">
                        <button type="submit"
                                class="btn inlinebtn?>"
                                aria-label="<?php echo t('Go'); ?>"
                                title="<?php echo t('Go'); ?>">
                            <?php echo t('Go'); ?>
                        </button>
                    </div>
                    <input type="hidden" name="currentPage" value="<?php echo $currentPage; ?>">
                </div>
            </form>
        </div>
    </div>

</div>