<?php /** @noinspection ALL */
$uuid = str_replace('.', '', uniqid('', true));
if ($sqlValue && 2 == sizeof(explode(' ', $sqlValue))) {
    list($date, $time) = explode(' ', $sqlValue);
} else {
    $date = '';
    $time = '';
}
?>
<span id="<?php echo $uuid; ?>">
    <input class="datetime" type="hidden" name="<?php echo $postname; ?>" value="<?php echo $sqlValue; ?>"/>

    <input class="date" type="date" value="<?php echo $date; ?>"/>

    <input class="time" type="time" value="<?php echo $time; ?>"/>
</span>

<script type="text/javascript">
    (function ($) {
        $(function () {
            $('#<?php echo $uuid; ?> .date, #<?php echo $uuid; ?> .time').change(function () {
                $('#<?php echo $uuid; ?> .datetime').val($('#<?php echo $uuid; ?> .date').val() + " " + $('#<?php echo $uuid; ?> .time').val());
            })
        })
    })(jQuery)
</script>