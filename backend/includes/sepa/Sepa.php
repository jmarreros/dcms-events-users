<?php
namespace dcms\event\backend\includes\sepa;
use dcms\event\helpers\Helper;
use dcms\event\includes\Database;

class Sepa {

	public function init() {
		add_action( 'wp_ajax_dcms_ajax_locked_sepa', [ $this, 'locked_sepa' ] );
	}

	public function locked_sepa() {
		// Validate nonce
		Helper::validate_nonce( $_POST['nonce'], 'ajax-sepa' );

		$res = [
			'status'  => 0,
			'message' => "No hay nuevos registros para guardar"
		];

		wp_send_json( $res );
	}

	public function get_users_with_sepa() {
		$db = new Database();
		$rows = $db->get_users_with_sepa();

		foreach ( $rows as $key => $row ) {
			$rows[ $key ]['time'] = date_i18n( 'Y-m-d H:i:s', $row['unix_time'] );
			$rows[ $key ]['sepa_file_url'] = DCMS_SEPA_FILES_URL . $row['sepa_file'] ;
			$rows[ $key ]['sepa_locked'] = boolval($row['sepa_locked']);
		}

		return $rows;
	}
}



