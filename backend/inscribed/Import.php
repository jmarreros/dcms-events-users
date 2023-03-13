<?php

namespace dcms\event\backend\inscribed;

class Import {

	public function __construct() {
		add_action( 'wp_ajax_dcms_ajax_add_file_import_selected', [ $this, 'upload_import_file_selected' ] );
	}

	public function upload_import_file_selected() {
		$res = [
			'status'  => 1,
			'message' => "Se importo correctamente"
		];
		wp_send_json( $res );
	}

}