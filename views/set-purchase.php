<?php
/** @var string $event_name */
/** @var string $user_name */
/** @var array $children */
?>
<section class="setting-purchase">
    <h2><?= $event_name ?></h2>

    <p>Hola <?= $user_name ?> tienes los siguientes inscritos:</p>
    <ul class="user-children">
        <?php foreach ( $children as $child ) : ?>
            <li>
                <span class="child-name">
                    <?= $child['name'] ?>
                </span>
                <span class="item-event">
                    <a class="button" href="#">Eliminar</a>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="item-event buttons">
        <a class="btn button" href="#">Continuar con el pago</a>
    </div>
</section>

