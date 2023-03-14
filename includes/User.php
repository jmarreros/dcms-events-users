<?php

namespace dcms\event\includes;

// User class
class User {

	// Validate identify and pin
	public function validate_identify_and_pin( $identify, $pin ) {
		$db     = new Database();
		$result = $db->find_user_identify_pin( $identify, $pin );
		if ( ! count( $result ) || $result[0]['count'] != 2 ) {
			$res = [
				'status'  => 0,
				'message' => "Identificador o PIN no válido",
			];

			echo json_encode( $res );
			wp_die();
		}
	}


	// Make an Arr children data, for specific user and event
	public function get_arr_children_user( $id_user, $id_post ): array {
		$db       = new Database();
		$children = $db->get_children_user( $id_user, $id_post );

		$children_data = [];
		foreach ( $children as $child ) {
			$child_name                       = $child['name'];
			$child_identify                   = $child['identify'];
			$children_data[ $child_identify ] = $child_name;
		}

		return $children_data;
	}
}