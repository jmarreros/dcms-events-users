<?php
// call from list-events-user.php
// Pass:
// $children

$count_children = DCMS_MAX_CHILDREN;
?>

<?php if ( count($children) ==0 ) : ?>
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
<?php else: ?>
    <section class="container-children">
        <div class="message-top">
            <strong>Convivientes agregados:</strong>
        </div>
        <?php
            echo "<div class=></div>";
            foreach( $children as $child){
                echo "<div class='added-children'>";
                echo "➜ " . $child['identify'] . " - ". $child['name'];
                echo "</div>";
            }
        ?>
    </section>
<?php endif; ?>