<?php

namespace dcms\event\includes;

use dcms\event\helpers\Helper;
use PleskX\Api\Operator\EventLog;

// Event class
class Event {
	public function __construct() {
		// Add user event
		add_action( 'wp_ajax_dcms_ajax_update_join', [ $this, 'join_user_event' ] );

		//Children
		add_action( 'wp_ajax_dcms_ajax_validate_children', [ $this, 'validate_identify_children' ] );
		add_action( 'wp_ajax_dcms_ajax_add_children', [ $this, 'add_children_event' ] );
		add_action( 'wp_ajax_dcms_ajax_remove_child', [ $this, 'remove_child_event' ] );

		// Mailing
		add_action( 'wp_ajax_dcms_ajax_resend_mail_join_event', [ $this, 'resend_email_join_event' ] );

		// Payment
		add_action( 'wp_ajax_dcms_ajax_continue_with_payment', [ $this, 'continue_with_payment' ] );

	}

	// Update the participation of the Individual user in an event
	public function join_user_event() {
		// Validate nonce
		Helper::validate_nonce( $_POST['nonce'], 'ajax-nonce-event' );

		// Event data
		$id_post       = intval( $_POST['id_post'] );
		$event_title   = get_the_title( $id_post );
		$event_excerpt = get_the_excerpt( $id_post );

		//User data
		$obj_user = wp_get_current_user();
		$id_user  = $obj_user->ID;
		$name     = $obj_user->display_name;
		$email    = $obj_user->user_email;


		$joined = 1; // New condition, only allow joined

		$db     = new Database();
		$result = $db->save_join_user_to_event( $id_post, $id_user );

		// Validate if updated rows > 0
		Helper::validate_updated( $result );

		//Send email user
		$this->send_email_join_event( $name, $email, $event_title, $event_excerpt );

		// Update user meta
		$db->update_count_user_meta( $id_user );

		// If all is ok
		$res = [
			'status'  => 1,
			'joined'  => $joined,
			'message' => "✅ Te has inscrito correctamente al Evento, <br> En unas horas recibirás en tu email la confirmación por parte del Club. <br> Si no lo recibes, no olvides revisar la bandeja de no deseados, Spam, y Promociones",
		];

		echo json_encode( $res );
		wp_die();
	}


	// Verify Identify and PIN
	public function validate_identify_children() {
		// Validate nonce
		Helper::validate_nonce( $_POST['nonce'], 'ajax-nonce-event-children' );

		$id_post  = intval( $_POST['id_post'] );
		$identify = $_POST['identify'] ?? null;
		$pin      = $_POST['pin'] ?? null;
		$id_user  = get_current_user_id();

		( new User() )->validate_identify_and_pin( $identify, $pin );

		$db = new Database();

		// Id user children
		$children_id = $result[0]['user_id'] ?? 0;

		// Validate if $identify user is yet in the event or if has been assigned
		$message = '';
		$joined  = $db->search_user_in_event( $children_id, $id_post );
		if ( $joined == 1 ) {
			$message = "El acompañante ya participa en el evento, seleccione otro";
		}
		if ( is_null( $joined ) ) {
			$message = "El acompañante no ha sido asignado a este evento, seleccione otro";
		}
		if ( $children_id == $id_user ) {
			$message = "Ingresa un acompañante que no seas tu mismo";
		}

		if ( $message != '' ) {
			$res = [
				'status'  => 0,
				'message' => $message,
			];

			echo json_encode( $res );
			wp_die();
		}

		// Return values
		$children_meta = $db->get_user_meta( $children_id );

		$children_name     = Helper::search_field_in_meta( $children_meta, 'name' );
		$children_lastname = Helper::search_field_in_meta( $children_meta, 'lastname' );
		$children_identify = Helper::search_field_in_meta( $children_meta, 'identify' );

		// If all is ok
		$res = [
			'status'   => 1,
			'name'     => $children_name . ' ' . $children_lastname,
			'id_user'  => $children_id,
			'identify' => $children_identify,
			'message'  => "Acompañante encontrado"
		];

		echo json_encode( $res );
		wp_die();
	}

	// Remove specif child from event
	public function remove_child_event() {
		// Validate nonce
		Helper::validate_nonce( $_POST['nonce'], 'ajax-nonce-event-children' );

		$id_user = intval( $_POST['id_user'] );
		$id_post = intval( $_POST['id_event'] );

		// Validate identify and pin
		$db = new Database();
		$db->remove_child_event( $id_user, $id_post );

		// If all is ok
		$res = [
			'status'  => 1,
			'message' => "Se eliminó correctamente"
		];

		echo json_encode( $res );
		wp_die();
	}

	// Add children
	public function add_children_event() {
		// Validate nonce
		Helper::validate_nonce( $_POST['nonce'], 'ajax-nonce-event-children' );

		// Event data
		$id_post       = intval( $_POST['id_post'] );
		$event_title   = get_the_title( $id_post );
		$event_excerpt = get_the_excerpt( $id_post );

		//User data
		$obj_user = wp_get_current_user();
		$id_user  = $obj_user->ID;
		$name     = $obj_user->display_name;
		$email    = $obj_user->user_email;
		$parent   = get_user_meta( $id_user, 'identify', true );

		// Children data
		$ids_children   = $_POST['children_data'];
		$count_children = count( $ids_children );
		$children_data  = [];

		$result = 0;
		$db     = new Database();
		if ( $ids_children && $count_children <= DCMS_MAX_CHILDREN ) {

			foreach ( $ids_children as $id_child ) {

				$result = $db->save_children( $id_child, $id_post, $parent, $id_user );

				//Children data
				$user_data                        = get_user_meta( $id_child );
				$child_name                       = $user_data['name'][0] . ' ' . $user_data['lastname'][0];
				$child_identify                   = $user_data['identify'][0];
				$children_data[ $child_identify ] = $child_name;
			}

			// Update user inscription
			$db->save_join_user_to_event( $id_post, $id_user, $parent );

			//Send email user
			$this->send_email_join_event( $name, $email, $event_title, $event_excerpt, $children_data );
		}

		Helper::validate_add_children( $result );

		// If all is ok
		$res = [
			'status'  => 1,
			'message' => "➜ Los acompañantes se agregaron correctamente <br> En unas horas recibirás en tu email la confirmación por parte del Club. <br> Si no lo recibes, no olvides revisar la bandeja de no deseados, Spam, y Promociones"
		];

		echo json_encode( $res );
		wp_die();

	}


	// Send mail join event
	private function send_email_join_event( $name, $email, $event_title, $event_excerpt = '', $convivientes = [] ): bool {
		$mail      = new Mail();
		$user_join = [
			'name'         => $name,
			'email'        => $email,
			'convivientes' => $convivientes
		];

		$event_join = [
			'title'   => $event_title,
			'excerpt' => $event_excerpt
		];

		return $mail->send_mail_template( 'inscription', $user_join, $event_join );
	}

	// public function for resending emails
	public function resend_email_join_event() {

		// Validate nonce
		Helper::validate_nonce( $_POST['nonce'], 'ajax-inscribed-selected' );

		// Event data
		$user_id   = intval( $_POST['userID'] );
		$event_id  = intval( $_POST['eventID'] );
		$user_name = $_POST['userName'];
		$email     = $_POST['email'];

		$event_data    = get_post( $event_id );
		$event_title   = $event_data->post_title;
		$event_excerpt = $event_data->post_excerpt;

		$data_children = ( new User() )->get_arr_children_user( $user_id, $event_id );

		$result = $this->send_email_join_event( $user_name, $email, $event_title, $event_excerpt, $data_children );

		if ( ! $result ) {
			$res = [
				'status'  => 0,
				'message' => "Ocurrió un problema en el reenvío del correo " . $email
			];
		} else {
			$res = [
				'status'  => 1,
				'message' => "ok"
			];
		}

		echo json_encode( $res );
		wp_die();
	}


	// Setting purchase form
	public function continue_with_payment() {
		global $woocommerce;

		// Validate nonce
		Helper::validate_nonce( $_POST['nonce'], 'ajax-nonce-event' );

		$id_user      = $_POST['id_user'] ?? 0;
		$id_event     = $_POST['id_event'] ?? 0;
		$children_set = $_POST['children'] ?? [];

		$db   = new Database();
		$data = $db->get_selected_event_user( $id_user, $id_event );

		$count_total = count( $children_set ) + 1;

		// Get and validate product associate with event
		$id_product = get_post_meta( $id_event, DCMS_EVENT_PRODUCT_ID, true );
		if ( ! $id_product ) {
			$res['message'] = "No hay un producto asignado al evento";
			wp_send_json( $res );
		}

		// Validating data sent for the user, id user and user assigned to event
		if ( get_current_user_id() == $id_user && ! empty( $data ) ) {

			$children_user = array_column( $db->get_children_user( $id_user, $id_event ), 'id_user' );

			// Validating if children have the event assigned
			if ( ! empty( $children_set ) ) {

				if ( ! empty( array_diff( $children_set, $children_user ) ) ) {
					$res['message'] = "Uno de los acompañantes no tiene asignado el evento";
					wp_send_json( $res );
				}
			}

			$children_deselected = array_diff( $children_user, $children_set );
			$db->deselect_children_event( $id_user, $children_deselected, $id_event );

			// Empty cart
			$woocommerce->cart->empty_cart();

			try {
				WC()->cart->add_to_cart( $id_product, $count_total );
				// Build redirection to cart
				$res = [
					'status'  => 1,
					'message' => "Redireccionando...",
					'url'     => wc_get_cart_url()
				];
			} catch ( \Exception $e ) {
				$res['message'] = "Hubo un error al agregar al carrito - " . $e->getMessage() ;
			}

		} else {
			$res['message'] = "El usuario conectado no coincide con el usuario del evento";
		}

		wp_send_json( $res );
	}

}

