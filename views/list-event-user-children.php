<?php
// call from list-events-user.php
// Pass:
// $children  - children data saved
// $lock_inscriptions

$count_children = DCMS_MAX_CHILDREN;
$id_event = $event->id_post;
?>

    <?php if ( count($children) == 0 ) : ?>

        <?php if ( ! $lock_inscriptions ): ?>
        <section class="container-question">
            <label>
                <input type="checkbox" class="question-children"name="question-<?= $id_event ?>" disabled> Quiero agregar convivientes
            </label>
        </section>
        <?php endif; ?>

    <?php else : ?>

        <section class="container-children-data">
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

        <?php if ( ! $lock_inscriptions ) : ?>
        <section class="container-question no-mark">
            <label>
                <input type="checkbox" class="question-children"name="question-<?= $id_event ?>" disabled> Editar convivientes
            </label>
        </section>
        <?php endif; ?>

    <?php endif; ?>



    <?php if ( ! $lock_inscriptions ) : // lock_inscriptions?>

        <section class="container-children">
            <div class="message-top">
                Ingresa los convivientes que quieres registrar (3 máximo):
            </div>
            <ul class="list-children">


            <?php if ( count($children) > 0): //saved elements ?>

                <?php foreach( $children as $child): ?>
                    <li>
                        <section class="message">
                        </section>
                        <div class="cinputs" style="display:none;">
                            <input class="cidentify" placeholder="Identificativo" type="text">
                            <input class="cpin" placeholder="PIN" type="password">
                        </div>
                        <div class="cdata">
                            <?= "➜ " . $child['name']; ?>
                        </div>
                        <div class="cactions">
                            <a href="#"
                                class="cclear child_db"
                                data-uid=<?= $child['id_user']?>
                                data-eid=<?= $id_event ?>
                                style="display:inline;"
                            >Eliminar
                            </a>
                            <button class="cvalidate button" style="display:none;">Buscar</button>
                        </div>
                    </li>
                <?php endforeach; ?>

            <?php endif; ?>


            <?php
            $resto = $count_children - count($children);
            for($i=0; $i < $resto; $i++ ): // new elements ?>
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
                Unirse con convivientes
            </button>

            <section class="add-children message">
            </section>
        </section>

    <?php endif; // $lock_inscriptions ?>


<?php // elseif ( count($children) > 0 ) : ?>

