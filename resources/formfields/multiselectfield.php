<?php /** @noinspection ALL */ ?>
<select name="<?= $postname ?>[]" multiple>
    <?php foreach ($options as $key => $optionValue) { ?>
        <option value="<?= $key ?>" <?= isset($sqlValue[$key]) ? "selected" : "" ?>>
            <?= t($optionValue) ?>
        </option>
    <?php } ?>
</select>