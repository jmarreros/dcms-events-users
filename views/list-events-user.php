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
				<?php
				foreach ( $events as $event ): ?>
                <li class="item-event">
                    <h3><?= $event->post_title ?></h3>
                    <section class="inscription-container">
						<?php
						// Validate joined by another user
						if ( $event->id_parent > 0 ) {
							$parent_user     = $db->get_user_meta( $event->id_parent );
							$parent_name     = Helper::search_field_in_meta( $parent_user, 'name' );
							$parent_lastname = Helper::search_field_in_meta( $parent_user, 'lastname' );
							echo "<p><strong>Inscrito por:</strong> " . $parent_name . " " . $parent_lastname . "</p>";
							continue;
						}

						$is_joined = $event->joined;
						if ( $is_joined ) {
							echo "<div class='message-joined'>";
							echo "Te has inscrito correctamente al evento, en unas horas recibirás en tu email la confirmación por parte del Club. <br> Si no lo recibes, no olvides revisar la bandeja de no deseados, Spam, y Promociones";
							echo "</div>";
						}

						// Event locked
						$lock_inscriptions = get_post_meta( $event->id_post, DCMS_LOCK_INSCRIPTIONS, true )
						                     || ! Helper::is_greater_than_today( $event->maximum_date );

						$enable_convivientes = get_post_meta( $event->id_post, DCMS_ENABLE_CONVIVIENTES, true );

						if ( $enable_convivientes ) {
							$children = $db->get_children_user( $event->id_user, $event->id_post );
							include 'list-event-user-children.php';
						}

                        include 'list-event-user.php';
						?>

                        <div class="description"><?= do_shortcode( $event->post_content ) ?></div>
                    </section>
                    </li>
					<?php endforeach; ?>
            </ul>
		<?php endif; ?>
    </section>
</section>
