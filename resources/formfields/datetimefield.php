<?php /** @noinspection ALL */
$uuid = str_replace(".", "", uniqid("", true));
if ($sqlValue && sizeof(explode(" ", $sqlValue)) == 2) {
    list($date, $time) = explode(" ", $sqlValue);
} else {
    $date = "";
    $time = "";
}
?>
<span id="<?= $uuid ?>">
    <input class="datetime" type="hidden" name="<?= $postname ?>" value="<?= $sqlValue ?>"/>

    <input class="date" type="date" value="<?= $date ?>"/>

    <input class="time" type="time" value="<?= $time ?>"/>
</span>

<script type="text/javascript">
    (function ($) {
        $(function () {
            $('#<?= $uuid ?> .date, #<?= $uuid ?> .time').change(function () {
                $('#<?= $uuid ?> .datetime').val($('#<?= $uuid ?> .date').val() + " " + $('#<?= $uuid ?> .time').val());
            })
        })
    })(jQuery)
</script>