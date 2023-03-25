<?php
/** @var string $event_name */
/** @var string $user_name */
/** @var int $id_event */
/** @var int $id_user */
/** @var array $children */
?>
<section class="setting-purchase">
    <h2><?= $event_name ?></h2>

    <p>Hola <?= $user_name ?> tienes los siguientes inscritos:</p>
    <ul class="user-children">
		<?php foreach ( $children as $child ) : ?>
            <li class="user-child"
                data-user="<?= $id_user ?>"
                data-child="<?= $child['id_user'] ?>"
                data-event="<?= $id_event ?>"
            >
                <span class="child-name">
                    <?= $child['name'] ?>
                </span>
                <a class="button remove" href="#">Eliminar</a>
                <a class="button add" href="#">Agregar</a>
            </li>
		<?php endforeach; ?>
    </ul>
    <div class="buttons-container">
        <div class="lds-ring" style="display:none;"><div></div><div></div><div></div><div></div></div>
        <a data-event="<?= $id_event ?>" data-user="<?= $id_user ?>" class="btn button" href="#">
            Continuar con el pago
        </a>
        <div class="message"></div>
    </div>
</section>

