<?php
// Web get this variables
// -----------------------
// $id_user
// $data
// $text_fields
// $editable_fields

use dcms\event\helpers\Helper;

?>
<section class="container-account-details">
    <?php if ( $data ): ?>
        <form id="frm-account-details" class="frm-account-details" method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>" >

            <table class="dcms-user-details">
            <?php foreach ($text_fields as $key => $field):
                    $value = Helper::search_field_in_meta($data, $key);?>
                    <tr>
                        <th><?= $field ?></th>
                        <?php if ( array_key_exists($key, $editable_fields) )  : ?>
                            <td><input id="<?= $key ?>" type="<?= $editable_fields[$key] ?>" value="<?= $value ?>" required maxlength="200" /></td>
                        <?php else: ?>
                            <td><?= $value ?></td>
                        <?php endif; ?>
                    </tr>
            <?php endforeach; ?>
            </table>
            <input type="hidden" name="action" value="save_account_details">
            <input class="button" type="submit" id="send" name="send" value="<?php _e('Actualizar datos', 'dcms-events-users') ?>">

            <section class="message" style="display:none;">
            </section>

            <!--spinner-->
            <div class="lds-ring" style="display:none;"><div></div><div></div><div></div><div></div></div>
        </form>
    <?php endif; ?>
</section>
