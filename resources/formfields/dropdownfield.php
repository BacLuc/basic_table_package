<?php /** @noinspection ALL */ ?>
<select name="<?php echo $postname; ?>">
    <option <?php echo null === $sqlValue ? 'selected' : ''; ?>/>
    <?php foreach ($options as $key => $optionValue) { ?>
        <option value="<?php echo $key; ?>" <?php echo $key === $sqlValue ? 'selected' : ''; ?>>
            <?php echo t($optionValue); ?>
        </option>
    <?php } ?>
</select>