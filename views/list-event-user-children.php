<?php
// call from list-events-user.php


// TODO:
/*
- Mostrar la cantidad de fila de controles de de hijos
- Cada fila debe tener un botón de validar
- Tras tener todos los registros validados se podrá guardar
*/

// $event
$count_children = $event->children;
?>

<?php if ($count_children > 0 ) : ?>
    <section class="container-children">
        <div class="message-top">
            Ingresa los acompañantes que quieres registrar:
        </div>
        <ul class="list-children">
        <?php for($i=0; $i < $count_children; $i++ ): ?>
            <li>
                <section class="message">
                </section>
                <div class="cinputs">
                    <input class="cidentify" placeholder="Identificativo">
                    <input class="cpin" placeholder="PIN">
                </div>
                <div class="cdata">
                    ➜ Jhon Mareros Guzmán
                </div>
                <div class="cactions">
                    <a href="#" class="cclear">Eliminar</a>
                    <a href="#" class="cvalidate button">Validar</a>
                </div>
            </li>
        <?php endfor; ?>
        </ul>

        <button type="button" class="button btn-join" >
            Agregar acompañantes
        </button>

        <section class="message">
        </section>
    </section>
<?php endif; ?>