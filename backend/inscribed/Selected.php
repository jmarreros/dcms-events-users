<?php

namespace dcms\event\backend\inscribed;

use dcms\event\helpers\Helper;
use dcms\event\includes\Database;
use dcms\event\includes\Mail;

class Selected {

	public function __construct() {
		add_action( 'wp_ajax_dcms_ajax_import_selected', [ $this, 'import_selected' ] );
	}

	public function import_selected() {
		// Validate nonce
		Helper::validate_nonce( $_POST['nonce'], 'ajax-inscribed-selected' );

		$res = [
			'status'  => 0,
			'message' => "No hay nuevos registros para guardar"
		];

		$identifies = $_POST['identifies'] ?? [];
		$event_id   = $_POST['event_id'] ?? 0;

		if ( count( $identifies ) > 0 ) {
			$users       = ( new Database() )->filter_users_event_selected_identifies( $event_id, $identifies );
			$event_name = get_the_title( $event_id );

			error_log(print_r($users,true));

			foreach ( $users as $user ) {
				// Not children
				if ( empty($user->id_parent)) {

				}
			}

			error_log( print_r( $data, true ) );
		}

		wp_send_json( $res );
	}


	// Send mail join event
	private function send_email_selected_notify( $name, $email, $event_title, $convivientes = [] ) {

		$mail = new Mail();

		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
		$subject = $options['dcms_subject_email_inscription'];
		$body    = $options['dcms_text_email_inscription'];
		$body    = str_replace( '%name%', $name, $body );
		$body    = str_replace( '%event_title%', $event_title, $body );
		$body    = str_replace( '%event_extracto%', $event_excerpt, $body );

		$str = '';
		if ( $convivientes ) {
			$str = "Convivientes: <br>";
			$str .= "<ul>";
			foreach ( $convivientes as $key => $value ) {
				$str .= "<li> ID: " . $key . " - " . $value . "</li>";
			}
			$str .= "</ul>";
		}
		$body = str_replace( '%convivientes%', $str, $body );


		return wp_mail( $email, $subject, $body, $headers );
	}

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