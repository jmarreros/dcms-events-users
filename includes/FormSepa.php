<?php

namespace dcms\event\includes;

use dcms\event\helpers\Helper;

class FormSepa {
	public function __construct() {
		add_action( 'wp_ajax_dcms_ajax_add_file_sepa', [ $this, 'add_file_sepa' ] );
	}

	public function add_file_sepa() {
		$res = [];
		Helper::validate_nonce( $_POST['nonce'] ?? '', 'ajax-nonce-sepa' );

		if ( isset( $_FILES['file'] ) ) {

			global $wp_filesystem;
			WP_Filesystem();

			$name_file = $_FILES['file']['name'];
			$tmp_name  = $_FILES['file']['tmp_name'];

			$this->validate_extension_file( $name_file );

//            $content_directory = $wp_filesystem->wp_content_dir() . 'uploads/archivos-subidos/';
//            $wp_filesystem->mkdir( $content_directory );
//
//            if( move_uploaded_file( $tmp_name, $content_directory . $name_file ) ) {
                $res = [
                    'status' => 1,
                    'message' => "El archivo se agregó correctamente"
                ];
//            }
//
//        } else {
//            $res = [
//                'status' => 0,
//                'message' => "Existe un error en la subida del archivo"
//            ];

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
}