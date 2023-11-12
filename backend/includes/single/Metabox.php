<?php

namespace dcms\event\backend\includes\single;

// Create Metaboxes, for configuration and maximum date
use dcms\event\includes\Database;

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

		//For maximum inscribed date
		add_meta_box( 'dcms_maximum_date_metaboxes',
			__( 'Fecha máxima compra', 'dcms-events-users' ),
			[ $this, 'show_metabox_maximum_date' ],
			DCMS_EVENT_CPT,
			'side',
			'low' );

	}

	// Show metabox
	public function show_metabox_configuration( $post ): void {
		$post_id = $post->ID;

		$enable_convivientes = get_post_meta( $post_id, DCMS_ENABLE_CONVIVIENTES, true );
		$lock_inscriptions   = get_post_meta( $post_id, DCMS_LOCK_INSCRIPTIONS, true );
		$product_id          = get_post_meta( $post_id, DCMS_EVENT_PRODUCT_ID, true );

		// List products
		$products = ( new Database() )->get_list_products();

		include_once( DCMS_EVENT_PATH . 'backend/views/single-event/metabox-configuration.php' );
	}


	public function show_metabox_maximum_date($post):void{
		$post_id = $post->ID;
		$user_groups = ( new Database() )->group_users_by_created_date($post_id);

		include_once( DCMS_EVENT_PATH . 'backend/views/single-event/metabox-maximum-date.php' );
	}

	// Save data metabox
	public function save_metabox_data( $post_id ): void {
		if ( get_post_type( $post_id ) === DCMS_EVENT_CPT ) {

			// Save configuration
			$enable_convivientes = isset( $_POST[ DCMS_ENABLE_CONVIVIENTES ] ) ? 1 : 0;
			$lock_inscriptions   = isset( $_POST[ DCMS_LOCK_INSCRIPTIONS ] ) ? 1 : 0;
			$product_id          = $_POST[ DCMS_EVENT_PRODUCT_ID ] ?? 0;

			update_post_meta( $post_id, DCMS_ENABLE_CONVIVIENTES, $enable_convivientes );
			update_post_meta( $post_id, DCMS_LOCK_INSCRIPTIONS, $lock_inscriptions );
			update_post_meta( $post_id, DCMS_EVENT_PRODUCT_ID, $product_id );

			// Save maximum date
			if ( isset( $_POST[ 'group_date' ] ) && isset($_POST[ 'group_id' ]) ) {
				$groups_maximum_date = [];
				foreach ( $_POST[ 'group_date' ] as $key => $date ) {
					$group_id = $_POST[ 'group_id' ][ $key ];
					$groups_maximum_date[ $group_id ] = empty($date) ? NULL : $date;
				}
				( new Database() )->update_maximum_date_per_group( $post_id, $groups_maximum_date );
			}
		}
	}
	
}