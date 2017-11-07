<?php

use Application\Block\BasicTableBlock\FieldTypes\FileField;
use Application\Block\BasicTableBlock\FieldTypes\SelfSaveInterface;

?>

<div class="table-responsive basic_table_package" id="<?php echo $controller->getHTMLId(); ?>div">


    <?php
    /**
     * @var $controller \Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged\Controller
     */
    $tabledata = $controller->displayTable();

    ?>
    <div>
        <?php echo $controller->getHeader($view); ?>
    </div>
    <?php echo $controller->getTableControlButtons($this); ?>

    <?php
    if (strlen($errorMessage) > 0) {
        echo '<div class="errormessage alert-danger">
                ' . $errorMessage . '
                </div>
            ';
    }
    ?>
    <table id='<?php echo $controller->getHTMLId(); ?>' class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <?php
            $fields = $controller->getFields();

            foreach ($fields as $fieldname => $type) {

                if ($fieldname == 'id') {

                    ?>
                    <th width='20%' data-column-id="commands" data-formatter="commands"
                        data-sortable="false"><?php echo t('Action'); ?></th>


                <?php } else {
                    if ($type->showInTable()) {
                        ?>

                        <th data-column-id='<?php echo $type->getPostName(); ?>'
                            data-formatter="<?php echo (new ReflectionClass($type))->getShortName(); ?>"><?php echo t($type->getLabel()); ?></th>
                        <?php
                    }
                }
            } ?>

        </tr>
        </thead>
        <tbody>


        <?php

        foreach ($tabledata as $row) {
            echo '<tr>';

            foreach ($fields as $colname => $field) {
                if ($colname == 'id') {
                    echo $controller->getActions($view, $row);

                } else {
                    if ($field->showInTable()) {
                        if ($fields[$colname] instanceof SelfSaveInterface) {
                            $field->setRowId($row['id']);
                        } else {
                            $field->setSQLValue($row[$colname]);
                        }
                        //var_dump($field);
                        echo '<td>' . $field->getTableView() . '</td>';
                    }
                }
            }

        }
        echo '</tbody></table>';

        ?>

        <?php echo $controller->getTableControlButtons($this); ?>


</div>

<script type='text/javascript'>

    $(document).ready(function (e) {


        $('#<?php echo $controller->getHTMLId();?>').bootgrid({
            columnSelection: false,
            rowCount: -1,
            formatters: {
                /**
                 * TODO figure out what i hacked here and extract of library, when possible
                 * Normally, bootgrid takes the text value of the td element. The library is changed that it takes the hmlt(). And the formatters again take the text() of the data, retrieved form the text elemet.
                 * but for the commands, we need the html we provided.
                 * @param column
                 * @param row
                 * @returns string
                 */
                commands: function (column, row) {
                    return row[column.id];
                },

                /**
                 * Normally, bootgrid takes the text value of the td element. The library is changed that it takes the hmlt(). And the formatters again take the text() of the data, retrieved form the text elemet.
                 * but for the FileField, we need the html we provided.
                 * @param column
                 * @param row
                 * @returns {*}
                 */
                FileField: function (column, row) {
                    return row[column.id];
                }
            }
        });
        $('#<?php echo $controller->getHTMLId();?>div div.tablecontrols .actionbutton.importcsv').click(function (e) {
            $(this).parents(".tablecontrols").find(".csvfile-field")
                .click()
                .change(function (e) {
                    if ($(this).val().length > 0) {
                        $(this).parent().submit();
                    }
                    $(this).off("change");
                })
            ;
        });

    });

</script>
