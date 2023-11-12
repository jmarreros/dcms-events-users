<?php
/** @var object $user_groups */
?>
<section class="dcms-maximum-date">
    <p>
        Establecer la fecha máxima de compra para cada grupo de usuarios.
    </p>
    <ul>
		<?php foreach ( $user_groups as $key => $group ): ?>
            <li>
                <label title="Grupo asignado al evento el: <?= $group->group_date ?>">
					<?php
		    			$maximum_date = $group->maximum_date ? date( 'Y-m-d', strtotime( $group->maximum_date ) ) : '';
					?>
                    <strong>Grupo <?= $key + 1 ?> :</strong>
                    <input name="group_date[]" id="group-<?= $key ?>" value="<?= $maximum_date ?>" type="date">
                    <input type="hidden" name="group_id[]" value="<?= $group->group_date ?>">
                </label>
            </li>
		<?php endforeach; ?>
    </ul>
</section>