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
	public function data_selected_user_event($id_user, $id_event):array{
		return (new Database)->get_selected_event_user($id_user, $id_event);
	}

}
