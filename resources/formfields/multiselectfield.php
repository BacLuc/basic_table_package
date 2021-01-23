<?php /** @noinspection ALL */ ?>
<select name="<?php echo $postname; ?>[]" multiple>
    <?php foreach ($options as $key => $optionValue) { ?>
        <option value="<?php echo $key; ?>" <?php echo isset($sqlValue[$key]) ? 'selected' : ''; ?>>
            <?php echo t($optionValue); ?>
        </option>
    <?php } ?>
</select>