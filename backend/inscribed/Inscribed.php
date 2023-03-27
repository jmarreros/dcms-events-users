<?php

namespace dcms\event\backend\inscribed;

use dcms\event\helpers\Helper;
use dcms\event\includes\Database;
use dcms\event\includes\Mail;
use dcms\event\includes\User;

class Inscribed {

	public function __construct() {
		add_action( 'wp_ajax_dcms_ajax_resend_mail_join_event', [ $this, 'resend_email_join_event' ] );
	}

	// public function for resending emails
	public function resend_email_join_event() {

		// Validate nonce
		Helper::validate_nonce( $_POST['nonce'], 'ajax-inscribed-selected' );

		// Event data
		$user_id   = intval( $_POST['userID'] );
		$event_id  = intval( $_POST['eventID'] );
		$user_name = $_POST['userName'];
		$email     = $_POST['email'];

		$event_data    = get_post( $event_id );
		$event_title   = $event_data->post_title;
		$event_excerpt = $event_data->post_excerpt;

		$data_children = ( new User() )->get_arr_children_user( $user_id, $event_id );

		$result = ( new Mail )->send_email_join_event( $user_name, $email, $event_title, $event_excerpt, $data_children );

		if ( ! $result ) {
			$res = [
				'status'  => 0,
				'message' => "Ocurrió un problema en el reenvío del correo " . $email
			];
		} else {
			$res = [
				'status'  => 1,
				'message' => "ok"
			];
		}

		echo json_encode( $res );
		wp_die();
	}


	// Show inscribed screen
	public function get_inscribed_screen() {

		$db = new Database();

		// Select events
		$id_event         = $_POST['select_event'] ?? 0;
		$available_events = $db->get_avaiable_events();

		if ( ! $id_event && count( $available_events ) ) {
			$id_event = $available_events[0]->ID; // get the recent available event
		}

		// Get subscribed users
		if ( $id_event ) {
			$subscribed_users = $db->select_inscribed_users_event( $id_event );
			$selected_users   = $db->select_selected_users_event( $id_event );
		}

		$fields_inscribed_table = Helper::get_inscribed_user_fields();
		include_once( DCMS_EVENT_PATH . 'backend/views/inscribed-selected/inscribed-selected.php' );
	}
}