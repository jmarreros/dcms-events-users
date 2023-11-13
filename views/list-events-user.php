<?php
// Web get this variables
// -----------------------
// $id_user
// $events

use dcms\event\helpers\Helper;

?>
<section class="container-list-events">

    <section class="top-list">
        <div class="lds-ring" style="display:none;">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div><!--spinner-->
        <section class="message message-join-event" style="display:none;">
        </section>
    </section>

    <section class="body-list">
		<?php if ( $events ) : ?>
            <ul class="list-events">
				<?php foreach ( $events as $event ): ?>
					<?php
					// metadata event, get enable convivientes
					$post_id = $event->id_post;

					$enable_convivientes = get_post_meta( $post_id, DCMS_ENABLE_CONVIVIENTES, true );

					$is_grater_than_today = Helper::is_greater_than_today( $event->maximum_date );
					$lock_inscriptions    = get_post_meta( $post_id, DCMS_LOCK_INSCRIPTIONS, true ) || ! $is_grater_than_today;

					$has_parent = $event->id_parent > 0;
					$is_joined  = $event->joined;
					?>

                    <li class="item-event">

                        <h3><?= $event->post_title ?></h3>

						<?php if ( false && ! $has_parent && ! $lock_inscriptions ) : ?>
                            <!-- <section class="terms-conditions">
                                <label><input type="checkbox" class="event-conditions">
                                Aceptar la <a href="/declaracion-responsable/" target="_blank">Declaración de Responsabilidad</a> para habilitar el evento.
                                </label>
                            </section> -->
						<?php endif; ?>

						<?php
						$text_button = '';
						if ( $is_joined ):
							$text_button = __( 'Inscrito al evento', 'dcms-events-users' );
						else:
							if ( $lock_inscriptions ) {
								$text_button = __( 'Evento bloqueado', 'dcms-events-users' );
							} else {
								$text_button = __( 'Unirse al evento', 'dcms-events-users' );
							}
						endif;
						?>

                        <section class="inscription-container">
							<?php

							// Show children for the event
							if ( $enable_convivientes && ! $has_parent ) {
								// Call view
								$children = $db->get_children_user( $event->id_user, $event->id_post );
								include 'list-event-user-children.php';
							}
							?>

							<?php
							// Show Parent
							if ( $has_parent ) {
								$parent_user     = $db->get_user_meta( $event->id_parent );
								$parent_name     = Helper::search_field_in_meta( $parent_user, 'name' );
								$parent_lastname = Helper::search_field_in_meta( $parent_user, 'lastname' );

								echo "<p><strong>Inscrito por:</strong> " . $parent_name . " " . $parent_lastname . "</p>";
							} ?>


							<?php
							if ( $is_joined ) {
								echo "<div class='message-joined'>";
								echo "Te has inscrito correctamente al evento, en unas horas recibirás en tu email la confirmación por parte del Club. <br> Si no lo recibes, no olvides revisar la bandeja de no deseados, Spam, y Promociones";
								echo "</div>";
							}
							?>
                            <button
                                    type="button"
                                    class="button btn-join <?= $is_joined ? 'nojoin' : 'join' ?>"
                                    data-id="<?= $event->id_post ?>"
                                    data-joined="<?= $is_joined ?>"
								<?php if ( $is_joined || $lock_inscriptions )
									echo "disabled" ?>
                            >
								<?= $text_button ?>
                            </button>

                            <div class="description"><?= do_shortcode( $event->post_content ) ?></div>

                        </section>
                    </li>

				<?php endforeach; ?>
            </ul>
		<?php endif; ?>
    </section>
</section>
