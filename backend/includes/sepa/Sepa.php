<?php


namespace dcms\event\backend\includes\sepa;

use dcms\event\includes\Database;

class Sepa {
	public function get_users_with_sepa() {
		$db = new Database();
		$rows = $db->get_users_with_sepa();

		foreach ( $rows as $key => $row ) {
			$rows[ $key ]['time'] = date_i18n( 'Y-m-d H:i:s', $row['unix_time'] );
			$rows[ $key ]['sepa_file_url'] = DCMS_SEPA_FILES_URL . $row['sepa_file'] ;
		}

		return $rows;
	}
}