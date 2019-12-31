<?php /** @noinspection ALL */ ?>
<select name="<?= $postname ?>">
    <option <?= $sqlValue === null ? "selected" : "" ?>/>
    <?php foreach ($options as $key => $optionValue) { ?>
        <option value="<?= $key ?>" <?= $key === $sqlValue ? "selected" : "" ?>>
            <?= t($optionValue) ?>
        </option>
    <?php } ?>
</select>