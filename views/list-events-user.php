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

                        $show_fase1 = false;
                        $show_fase2 = false;

                        if ( (new DateTime())->format('Y-m-d') <= (new DateTime($fase1))->format('Y-m-d') ) $show_fase1 = true;
                        if ( (new DateTime())->format('Y-m-d') <= (new DateTime($fase2))->format('Y-m-d') ) $show_fase2 = true;
                    ?>

                    <?php if ( ! $enable_fases || ( $enable_fases && $show_fase2 ) ) : ?>
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
                                    <label>Número de acompañantes: </label>
                                    <select class="select-children <?= $joined ?>" name="select-children"
                                        <?php if ( $event->joined ) echo "disabled" ?>
                                    >
                                        <option value="0" <?php selected( $event->children, 0) ?>>Ninguno</option>
                                        <option value="1" <?php selected( $event->children, 1) ?>>1</option>
                                        <option value="2" <?php selected( $event->children, 2) ?>>2</option>
                                        <option value="3" <?php selected( $event->children, 3) ?>>3</option>
                                    </select>
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

                            <div class="description"><?= $event->post_content ?></div>

                        </section>
                    </li>
                    <?php endif; //show fase2 ?>

                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
</section>
