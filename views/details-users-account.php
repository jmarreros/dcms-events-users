<?php
// Web get this variables
// -----------------------
// $id_user
// $data
// $text_fields
// $editable_fields
?>
<section class="container-account-details">
    <form id="frm-account-details" class="frm-account-details" method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>" >

        <table class="dcms-user-details">
        <?php
        foreach ($data as $item) {
            ?>
                <tr>
                    <th><?= $text_fields[$item->meta_key] ?></th>
                    <?php if ( array_key_exists($item->meta_key, $editable_fields) ) : ?>
                        <td><input id="<?= $item->meta_key ?>" type="<?= $editable_fields[$item->meta_key] ?>" value="<?= $item->meta_value ?>" required maxlength="200" /></td>
                    <?php else: ?>
                        <td><?= $item->meta_value ?></td>
                    <?php endif; ?>
                </tr>
            <?php
        }
        ?>
        </table>
        <input type="hidden" name="action" value="save_account_details">
        <input class="button" type="submit" id="send" name="send" value="<?php _e('Save', DCMS_EVENT_DOMAIN) ?>">

        <section class="message" style="display:none;">
        </section>

        <!--spinner-->
        <div class="lds-ring" style="display:none;"><div></div><div></div><div></div><div></div></div>
    </form>
</section>
