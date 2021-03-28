<?php
use dcms\event\helpers\Helper;

// $items
$data = Helper::transform_columns_arr($items);
$count = count($data);
?>
<section class="container-user-event">

    <div class="top-user-event">
        <section class="user-event-info">
            Total: <strong> <span class="total-info"><?= $count ?></span> </strong>
        </section>

        <section class="butons-user-event">
            <a class="btn-export button button-primary" href="http://abonadosporting.local/wp-admin/admin-post.php?action=process_export_list_customers&data=foobarid" target="_blank"><?php _e('Export all', DCMS_EVENT_DOMAIN) ?></a>
            <a id="open-add-customers" class="btn-add button button-primary"><?php _e('Add Customers', DCMS_EVENT_DOMAIN) ?></a>
        </section>
    </div>

    <section class="container-table-event">
        <table class="tbl-users-event">
                <tr>
                    <?php
                        echo "<th>ID</th>";
                        $fields = Helper::get_filter_fields();
                        foreach ($fields as $field) {
                            echo "<th> $field </th>";
                        }
                    ?>
                    <?php ?>
                </tr>

                <?php
                    if ( $data ):
                        foreach ($data as $item) {
                            echo "<tr>";
                            echo "<td>{$item['user_id']}</td>";
                            echo "<td>{$item['number']}</td>";
                            echo "<td>{$item['name']}</td>";
                            echo "<td>{$item['lastname']}</td>";
                            echo "<td>{$item['sub_type']}</td>";
                            echo "<td>{$item['soc_type']}</td>";
                            echo "</tr>";
                        }
                    endif;
                ?>

        </table>
        <input type="hidden" name="id_user_event" id="id_user_event" value="" />
    </section>
</section>

<?php include_once('modal-list-filter.php'); ?>
