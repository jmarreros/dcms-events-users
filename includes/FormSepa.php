<?php

namespace dcms\event\includes;

use dcms\event\helpers\Helper;

class FormSepa {
	public function init() {
		add_action( 'wp_ajax_dcms_ajax_add_file_sepa', [ $this, 'add_file_sepa' ] );
	}

	public function add_file_sepa() {
		$res = [
			'status'  => 0,
			'message' => "Existe un error en la subida del archivo"
		];

		Helper::validate_nonce( $_POST['nonce'] ?? '', 'ajax-nonce-sepa' );

		if ( isset( $_FILES['file'] ) ) {

			global $wp_filesystem;
			WP_Filesystem();

			$name_file = $_FILES['file']['name'];
			$tmp_name  = $_FILES['file']['tmp_name'];

			$this->validate_extension_file( $name_file );
			$this->validate_user_login();

			$ext = wp_check_filetype( $name_file)['ext'];
			$name_file = current_time( 'timestamp' ) . get_current_user_id() . '.' . $ext;

			if ( ! file_exists( DCMS_SEPA_FILES_PATH ) ) {
				$wp_filesystem->mkdir( DCMS_SEPA_FILES_PATH );
			}

			if ( move_uploaded_file( $tmp_name, DCMS_SEPA_FILES_PATH . $name_file ) ) {
				// Update user metadata
				update_user_meta( get_current_user_id(), 'sepa_file', $name_file );
				$res = [
					'status'  => 1,
					'message' => "El archivo se agregó correctamente"
				];
			}
		}

		wp_send_json( $res );
	}

	// Extension file validation
	private function validate_extension_file( $name_file ) {
		$path_parts       = pathinfo( $name_file );
		$ext              = $path_parts['extension'];
		$allow_extensions = [ 'txt', 'pdf' ];

		if ( ! in_array( $ext, $allow_extensions ) ) {
			$res = [
				'status'  => 0,
				'message' => "Extensión de archivo no permitida"
			];
			wp_send_json( $res );
		}
	}

	// User conected validation
	private function validate_user_login(){
		if ( ! get_current_user_id() ){
			$res = [
				'status'  => 0,
				'message' => "Usuario no conectado"
			];
			wp_send_json( $res );
		}
	}

}