<?php

namespace dcms\event\backend\inscribed;

use dcms\event\helpers\Helper;
use dcms\event\includes\Database;

class Inscribed {

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
			$subscribed_users = $db->select_users_event_export( $id_event, true );
		}

		$fields_table = Helper::get_inscribed_user_fields();
		include_once( DCMS_EVENT_PATH . 'backend/views/inscribed-selected/inscribed-selected.php' );
	}
}