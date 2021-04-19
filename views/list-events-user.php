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
                    <?php $joined = $event->joined ? 'nojoin': 'join'; ?>
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

                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
</section>
