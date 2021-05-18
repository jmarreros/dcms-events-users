<?php
// call from list-events-user.php

// $event
$count_children = $event->children;
?>

<?php if ($count_children > 0 ) : ?>
    <section class="container-children">
        <div class="message-top">
            Ingresa los convivientes que quieres registrar:
        </div>
        <ul class="list-children">
        <?php for($i=0; $i < $count_children; $i++ ): ?>
            <li>
                <section class="message">
                </section>
                <div class="cinputs">
                    <input class="cidentify" placeholder="Identificativo" type="text">
                    <input class="cpin" placeholder="PIN" type="password">
                </div>
                <div class="cdata">
                </div>
                <div class="cactions">
                    <a href="#" class="cclear">Eliminar</a>
                    <button class="cvalidate button">Buscar</button>
                </div>
            </li>
        <?php endfor; ?>
        </ul>

        <button type="button" class="button btn-add-children" >
            Agregar convivientes
        </button>

        <section class="add-children message">
        </section>
    </section>
<?php endif; ?>