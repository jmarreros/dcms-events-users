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



