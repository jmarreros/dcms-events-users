<?php
// Web get this variables
// -----------------------
// $id_user
// $events

?>
<section class="container-list-events">

        <section class="top-list">
            <div class="lds-ring" style="display:none;"><div></div><div></div><div></div><div></div></div><!--spinner-->
            <section class="message" style="display:none;">
            </section>
        </section>

        <section class="body-list">
            <?php if ($events) :?>
                <ul class="list-events">
                <?php foreach ($events as $event):?>
                    <?php
                        // joined
                        $joined = $event->joined ? 'nojoin': 'join';

                        // metadata event, fases and dates limit
                        $post_id = $event->id_post;

                        $enable_fases = get_post_meta($post_id, DCMS_ENABLE_FASES, true );
                        $fase1 = get_post_meta($post_id, DCMS_FASE_1, true );
                        $fase2 = get_post_meta($post_id, DCMS_FASE_2, true );

                        // Default values
                        $show_fase1 = false;
                        $show_fase2 = false;

                        if ( (new DateTime())->format('Y-m-d') <= (new DateTime($fase1))->format('Y-m-d') ) $show_fase1 = true;
                        if ( (new DateTime())->format('Y-m-d') <= (new DateTime($fase2))->format('Y-m-d') ) $show_fase2 = true;

                        // error_log(print_r($enable_fases,true));
                        // error_log(print_r($show_fase1,true));
                        // error_log(print_r($show_fase2,true));
                        // error_log(print_r($event,true));
                    ?>

                    <?php if (  ! $enable_fases ||
                                ( $enable_fases && $show_fase1 && ! $event->joined ) ||
                                ( $enable_fases && $show_fase2 && $event->joined ) ) : ?>
                    <li class="item-event">

                        <h3><?= $event->post_title ?></h3>

                        <?php if ( ! $event->joined ) : ?>
                            <section class="terms-conditions">
                                <label><input type="checkbox" id="event-conditions">
                                Aceptar la <a href="/declaracion-responsable/" target="_blank" checked=''>Declaración de Responsabilidad</a> para habilitar el evento.
                                </label>
                            </section>
                        <?php endif; ?>

                        <?php
                            $text_button = '';
                            if ( $event->joined ):
                                $text_button = __('Inscrito al evento', 'dcms-events-users');
                            else:
                                $text_button = __('Unirse al evento', 'dcms-events-users');
                            endif;
                        ?>

                        <section class="inscription-container">

                            <?php if ( $enable_fases && $show_fase1 ): ?>

                                <section class="add-children">
                                    <label>Número de convivientes: </label>

                                    <div class="select-children" >
                                        <label><input type="radio" name="count<?= $post_id ?>"  value="0" <?php checked( $event->children, 0); ?> disabled> Ninguno </label>
                                        <label><input type="radio" name="count<?= $post_id ?>"  value="1" <?php checked( $event->children, 1); ?> disabled> 1 </label>
                                        <label><input type="radio" name="count<?= $post_id ?>"  value="2" <?php checked( $event->children, 2); ?> disabled> 2 </label>
                                        <label><input type="radio" name="count<?= $post_id ?>"  value="3" <?php checked( $event->children, 3); ?> disabled> 3 </label>
                                    </div>

                                </section>

                            <?php endif; // fase1 ?>

                            <button
                                type="button"
                                class="button btn-join <?= $joined ?>"
                                data-id="<?= $event->id_post ?>"
                                data-joined="<?= $event->joined ?>"
                                disabled
                            >
                                <?= $text_button ?>
                            </button>

                            <?php
                            // Show children for the event
                            if ( $enable_fases && ! $show_fase1) {
                                // Call view
                                include 'list-event-user-children.php';
                            }
                            ?>

                            <div class="description"><?= $event->post_content ?></div>

                        </section>
                    </li>
                    <?php endif; //show fase2 ?>

                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
</section>
