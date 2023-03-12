<?php

namespace dcms\event\backend\single;

// Create Metabox for dates event in backend
class Metabox {

	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'create_metaboxes' ] );
		add_action( 'save_post', [ $this, 'save_metabox_data' ] );
	}

	public function create_metaboxes(): void {

		//For enable/disable convivientes behavior
		add_meta_box( 'dcms_conviviente_metaboxes',
			__( 'Opciones Evento', 'dcms-events-users' ),
			[ $this, 'show_metabox_configuration' ],
			DCMS_EVENT_CPT,
			'side',
			'low' );
	}

	// Show metabox
	public function show_metabox_configuration( $post ): void {
		$post_id = $post->ID;

		$enable_convivientes = get_post_meta( $post_id, DCMS_ENABLE_CONVIVIENTES, true );
		$lock_inscriptions   = get_post_meta( $post_id, DCMS_LOCK_INSCRIPTIONS, true );

		include_once( DCMS_EVENT_PATH . 'backend/views/single-event/metabox.php' );
	}

	// Save data metabox
	public function save_metabox_data( $post_id ): void {
		if ( get_post_type( $post_id ) === DCMS_EVENT_CPT ) {

			$enable_convivientes = isset( $_POST[ DCMS_ENABLE_CONVIVIENTES ] ) ? 1 : 0;
			$lock_inscriptions   = isset( $_POST[ DCMS_LOCK_INSCRIPTIONS ] ) ? 1 : 0;

			update_post_meta( $post_id, DCMS_ENABLE_CONVIVIENTES, $enable_convivientes );
			update_post_meta( $post_id, DCMS_LOCK_INSCRIPTIONS, $lock_inscriptions );
		}
	}


}