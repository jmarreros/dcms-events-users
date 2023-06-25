<?php

namespace dcms\event\backend\includes\single;

use dcms\event\helpers\Helper;
use dcms\event\includes\Database;


class Single {

	public function __construct() {
		add_action( 'edit_form_advanced', [ $this, 'add_filter_area' ], 10, 1 );
		add_action( 'save_post_' . DCMS_EVENT_CPT, [ $this, 'save_list_filter' ], 10, 3 );
	}

	// Show filter area and users saved for event
	public function add_filter_area( $post ): void {
		$screen      = get_current_screen();
		$id_post     = $post->ID;
		$status_post = $post->post_status;
		$data        = [];

		if ( $screen->post_type == DCMS_EVENT_CPT ) {

			wp_enqueue_style( 'admin-event-style' );
			wp_enqueue_script( 'admin-single-event' );
			wp_localize_script( 'admin-single-event', 'dcms_vars', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );

			if ( $status_post != 'auto-draft' ) {

				$db   = new Database();
				$data = $db->select_users_event( $id_post );
			}

			$fields = Helper::get_filter_fields();
			$count  = count( $data );

			include_once( DCMS_EVENT_PATH . 'backend/views/single-event/single-list-filter.php' );
		}
	}

	// Save filter list
	public function save_list_filter( $post_id, $post, $update ): void {
		$ids_insert = [];
		$ids_remove = [];
		$db         = new Database();

		// Insert users
		if ( isset( $_POST['id_user_event'] ) && $_POST['id_user_event'] != '' ) {
			$ids_insert = explode( ',', $_POST['id_user_event'] );
		}
		if ( $ids_insert ) {
			$db->remove_before_insert( $post_id ); // Remove only not joined users
			$res = $db->save_users_event( $ids_insert, $post_id );
			if ( ! $res ) {
				error_log( 'Error to insert users in event' );
			}
		}

		// Remove specific users
		if ( isset( $_POST['id_user_event_remove'] ) && $_POST['id_user_event_remove'] != '' ) {
			$ids_remove = explode( ',', $_POST['id_user_event_remove'] );
		}
		if ( $ids_remove ) {
			$res = $db->remove_users_event( $post_id, $ids_remove );
			if ( ! $res ) {
				error_log( 'Error removing specific users' );
			}
		}

	}
}



