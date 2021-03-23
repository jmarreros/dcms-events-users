<section class="list-customers">
    <a class="btn-export button button-primary" href="http://abonadosporting.local/wp-admin/admin-post.php?action=process_export_list_customers&data=foobarid" target="_blank"><?php _e('Export all', DCMS_EVENT_DOMAIN) ?></a>
    <a class="btn-add button button-primary" onclick="openModal()"><?php _e('Add Customers', DCMS_EVENT_DOMAIN) ?></a>
    <div class="list">

    </div>
</section>

<?php include_once('modal-list-filter.php'); ?>
