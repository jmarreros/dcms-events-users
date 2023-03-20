<?php

namespace dcms\event\backend\inscribed;

use dcms\event\helpers\Helper;
use dcms\event\includes\Database;
use dcms\event\includes\Mail;
use dcms\event\includes\User;

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

		if ( is_array( $identifies ) && count( $identifies ) > 0 ) {
			$selections = ( new Database() )->filter_users_event_selected_identifies( $event_id, $identifies );

			$mail = new Mail();
			$user = new User();
			$db   = new Database();

			$event_sel = [
				'id'      => $event_id,
				'title'   => get_the_title( $event_id ),
				'excerpt' => get_the_excerpt( $event_id )
			];

			foreach ( $selections as $item ) {

				// Not send email to child
				if ( empty( $item->id_parent ) ) {
					$user_sel = [
						'id'           => $item->id_user,
						'name'         => $item->name,
						'email'        => $item->email,
						'convivientes' => $item->children ? $user->get_arr_children_user( $item->id_user, $event_id ) : []
					];

					$mail->send_mail_template( 'selection', $user_sel, $event_sel );
				}

				$db->selected_event_user( $item->id );
			}

		}

		wp_send_json( $res );
	}

	// Get data selected user event
	public function data_selected_user_event($id_user, $id_event){

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