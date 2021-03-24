<?php
// Web get this variables
// -----------------------
// $id_user
// $data
// $text_fields
?>
<table class="user-details">
<?php
foreach ($data as $item) {
    ?>
        <tr>
            <th><?= $text_fields[$item->meta_key] ?><th>
            <td><?= $item->meta_value ?></td>
        </tr>
    <?php
}
?>
</table>
