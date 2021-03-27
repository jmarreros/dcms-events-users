<?php
use dcms\event\helpers\Helper;

?>
<section class="container-user-event">

    <section class="butons-user-event">
        <a class="btn-export button button-primary" href="http://abonadosporting.local/wp-admin/admin-post.php?action=process_export_list_customers&data=foobarid" target="_blank"><?php _e('Export all', DCMS_EVENT_DOMAIN) ?></a>
        <a id="open-add-customers" class="btn-add button button-primary"><?php _e('Add Customers', DCMS_EVENT_DOMAIN) ?></a>
    </section>

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
        </table>

    </section>
</section>

<?php include_once('modal-list-filter.php'); ?>
