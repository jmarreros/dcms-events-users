<?php
/** @var object $user_groups */
?>
<section class="dcms-maximum-date">
	<p>
	Grupos de usuarios de acuerdo a la fecha asignación a este evento
	</p>
	<ul>
        <?php foreach($user_groups as $key => $group): ?>
            <li>
                <label title="Agregado el: <?= $group->group_date ?>">
                    <strong>Grupo <?= $key + 1 ?></strong>
                    <input name="group_date[]" id="group-<?= $key  ?>" value="<?= $group->maximum_date ?>" type="datetime-local">
                </label>
            </li>
        <?php endforeach; ?>
	</ul>
</section>