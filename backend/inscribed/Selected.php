<?php

namespace dcms\event\backend\inscribed;

use dcms\event\helpers\Helper;

class Selected {

	public function __construct() {
		add_action( 'wp_ajax_dcms_ajax_import_selected', [ $this, 'import_selected' ] );
	}

	public function import_selected() {
		// Validate nonce
		Helper::validate_nonce( $_POST['nonce'], 'ajax-inscribed-selected' );

		$identifies = $_POST['identifies'] ?? [];
		$event_id   = $_POST['event_id'] ?? 0;

		$identifies = join( ',', $identifies );

		error_log( print_r( $event_id, true ) );
		error_log( print_r( $identifies, true ) );

		$res = [
			'status'  => 0,
			'message' => "Se guardaron los datos"
		];
		wp_send_json( $res );
	}


//	// Send email with identify and pin
//	private function send_email_pin( $email, $identify, $pin ){
//		$options = get_option( 'dcms_pin_options' );
//
//		$headers = ['Content-Type: text/html; charset=UTF-8'];
//		$subject = $options['dcms_subject_email'];
//		$body    = $options['dcms_text_email'];
//		$body = str_replace( '%id%', $identify, $body );
//		$body = str_replace( '%pin%', $pin, $body );
//
//		return wp_mail( $email, $subject, $body, $headers );
//	}
//
}


//
//public function upload_import_file_selected(): void {
//	// Validate nonce
//	Helper::validate_nonce( $_POST['nonce'], 'ajax-inscribed-selected' );
//
//	$res = [
//		'status'  => 0,
//		'message' => "Existe un error en la subida del archivo"
//	];
//
//	if ( isset( $_FILES['file'] ) ) {
//
//		global $wp_filesystem;
//		WP_Filesystem();
//
//		$name_file = $_FILES['file']['name'];
//		$tmp_name  = $_FILES['file']['tmp_name'];
//
//		$this->validate_extension_file($name_file);
//
//		$content_directory = $wp_filesystem->wp_content_dir() . 'uploads/tmp-files/';
//		$wp_filesystem->mkdir( $content_directory );
//
//		if ( move_uploaded_file( $tmp_name, $content_directory . $name_file ) ) {
//			$res = [
//				'status'  => 1,
//				'message' => "El archivo se agregó correctamente"
//			];
//		}
//
//	}
//
//	wp_send_json( $res );
//}
//
//
//// Extension file validation
//private function validate_extension_file( $name_file ): void {
//	$path_parts       = pathinfo( $name_file );
//	$ext              = $path_parts['extension'];
//	$allow_extensions = [ 'xlsx' ];
//
//	if ( ! in_array( $ext, $allow_extensions ) ) {
//		$res = [
//			'status'  => 0,
//			'message' => "Extensión de archivo no permitida"
//		];
//		wp_send_json( $res );
//	}
//
//}