<?php
/**
 * define the block add/edit view here
 */


defined('C5_EXECUTE') or die("Access Denied."); ?>
<style type="text/css">
    div.action-options {
        position: relative;
        border-bottom: 1px solid #ddd;
        margin-bottom: 3px;
    }

    div.action-options img {
        position: absolute;
        top: 3px;
        right: 0px;
    }

</style>
<div class="ccm-ui basic-table-edit">

    <?php
    $blockOptions = $controller->getBlockOptions();
    $counter = 0;
    foreach ($blockOptions as $optionnum => $OptionType) {


        echo $OptionType->getFormView($form);

    }

    ?>


</div>
<script type="text/javascript">
    Concrete.event.fire('survey-edit-open');
</script>
