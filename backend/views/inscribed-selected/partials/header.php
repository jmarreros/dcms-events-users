<header>
	<form method="post" id="frm-filter" class="frm-filter" action="">
		<div class="container-filter">
			<label for="select_event">Seleccionar evento: </label>
			<?php if ( count( $available_events ) ) : ?>
				<?php
				echo "<select name='select_event' id='select_event'>";
				foreach ( $available_events as $event ) {
					echo "<option value='" . $event->ID . "' ";
					echo selected( $id_event, $event->ID ) . " >";
					echo $event->post_title;
					echo "</option>";
				}
				echo "</select>";
				?>
				<button type="submit" id="btn-filter-event" class="btn-search button button-primary">
					Filtrar
				</button>
			<?php else : ?>
				<strong>No hay eventos activos</strong>
			<?php endif; ?>
		</div>
	</form>
</header>