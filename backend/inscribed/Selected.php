<?php

namespace dcms\event\backend\inscribed;

class Selected {

	public function __construct() {
		add_action( 'wp_ajax_dcms_ajax_read_import_file', [ $this, 'read_import_file' ] );
	}

	public function read_import_file() {
		$res = [
			'status'  => 1,
			'message' => "Se importo correctamente"
		];
		wp_send_json( $res );
	}

}