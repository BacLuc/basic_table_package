<?php /** @noinspection ALL */ ?>
<div class="table-responsive basic_table_package">

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