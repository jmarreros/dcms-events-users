<?php
// Web get this variables
// -----------------------
// $id_user
// $events

?>
<section class="container-list-events">

        <section class="top-list">
            <div class="lds-ring" style="display:none;"><div></div><div></div><div></div><div></div></div><!--spinner-->
            <section class="message message-join-event" style="display:none;">
            </section>
        </section>

        <section class="body-list">
            <?php if ( $events ) :?>
                <ul class="list-events">
                <?php foreach ($events as $event):?>
                    <?php
                        // joined
                        $joined = $event->joined ? 'nojoin': 'join';

                        // metadata event, get enable convivientes
                        $post_id = $event->id_post;
                        $enable_convivientes = get_post_meta($post_id, DCMS_ENABLE_CONVIVIENTES, true );
                    ?>

                    <li class="item-event">

                        <h3><?= $event->post_title ?></h3>

                        <section class="terms-conditions">
                            <label><input type="checkbox" class="event-conditions">
                            Aceptar la <a href="/declaracion-responsable/" target="_blank">Declaración de Responsabilidad</a> para habilitar el evento.
                            </label>
                        </section>

                        <?php
                            $text_button = '';
                            if ( $event->joined ):
                                $text_button = __('Inscrito al evento', 'dcms-events-users');
                            else:
                                $text_button = __('Unirse al evento', 'dcms-events-users');
                            endif;
                        ?>

                        <section class="inscription-container">
                            <?php
                            // Show children for the event
                            if ( $enable_convivientes ) {
                                // Call view
                                $children =  $db->get_children_user($event->id_user, $event->id_post);
                                include 'list-event-user-children.php';
                            }
                            ?>

                            <button
                                type="button"
                                class="button btn-join <?= $joined ?>"
                                data-id="<?= $event->id_post ?>"
                                data-joined="<?= $event->joined ?>"
                                disabled
                            >
                                <?= $text_button ?>
                            </button>

                            <div class="description"><?= $event->post_content ?></div>

                        </section>
                    </li>

                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
</section>
