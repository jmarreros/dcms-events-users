<?php
// Vars to pass
// $items
// $id_post
// $data
// $count
// $fields

$count_joins = 0;
?>
<section class="container-user-event">

    <div class="top-user-event">
        <section class="user-event-info">
            Total: <strong> <span class="total-info"><?= $count ?></span> </strong>
        </section>

        <section class="butons-user-event">
            <a id="open-add-customers" class="btn-add button button-primary"><?php _e('Add Customers', DCMS_EVENT_DOMAIN) ?></a>
        </section>
    </div>

    <section class="container-table-event">
        <table class="tbl-users-event">
                <tr>
                    <?php
                        echo "<th>ID</th>";
                        foreach ($fields as $field) {
                            echo "<th> $field </th>";
                        }
                    ?>
                    <?php ?>
                </tr>

                <?php
                    if ( $data ):
                        $count_joins = 0;
                        foreach ($data as $item) {

                            $mark = '';
                            if ( $item['joined'] ){
                                $mark = 'class="join"';
                                $count_joins++;
                            }
                            $count_events = $item['dcms_count_event']??0;
                            echo "<tr {$mark}>";
                            echo "<td>{$item['user_id']}</td>";
                            echo "<td>{$item['number']}</td>";
                            echo "<td>{$item['name']}</td>";
                            echo "<td>{$item['lastname']}</td>";
                            echo "<td>{$item['sub_type']}</td>";
                            echo "<td>{$item['soc_type']}</td>";
                            echo "<td>{$count_events}</td>";
                            echo "</tr>";
                        }
                    endif;
                ?>
        </table>
        <input type="hidden" name="id_user_event" id="id_user_event" value="" />
    </section>

    <div class="bottom-user-event">
        <section class="user-event-info">
            <?= __('Total Joined: ', DCMS_EVENT_DOMAIN) ?><strong> <span class="total-info"><?= $count_joins ?></span> </strong>
        </section>

        <section class="butons-user-event">
            <a class="btn-export button button-primary" href="<?= admin_url() ?>admin-post.php?action=process_export_list_customers&id_post=<?= $id_post ?>" target="_blank"><?php _e('Export all', DCMS_EVENT_DOMAIN) ?></a>
            <a class="btn-export button button-primary" href="<?= admin_url() ?>admin-post.php?action=process_export_list_customers&id_post=<?= $id_post ?>&only_joined=1" target="_blank"><?php _e('Export Joined', DCMS_EVENT_DOMAIN) ?></a>
        </section>
    </div>

</section>

<?php include_once('modal-list-filter.php'); ?>
