<?php

namespace dcms\event\includes;

use dcms\event\helpers\Helper;

// Event class
class Event {
	public function __construct() {
		// Add user event
		add_action( 'wp_ajax_dcms_ajax_update_join', [ $this, 'join_user_event' ] );

		//Children
		add_action( 'wp_ajax_dcms_ajax_validate_children', [ $this, 'validate_identify_children' ] );
		add_action( 'wp_ajax_dcms_ajax_add_children', [ $this, 'add_children_event' ] );
		add_action( 'wp_ajax_dcms_ajax_remove_child', [ $this, 'remove_child_event' ] );

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
		Helper::validate_updated( $result ); // Validate if updated rows > 0

		// Update user meta
		$db->update_count_user_meta( $id_user );


		// Direct purchase process
		$this->process_direct_purchase($id_post, $id_user);


		//Send email user
		( new Mail )->send_email_join_event( $name, $email, $event_title, $event_excerpt );

		// If all is ok
		$res = [
			'status'  => 1,
			'joined'  => $joined,
			'message' => "✅ Te has inscrito correctamente al Evento, <br> En unas horas recibirás en tu email la confirmación por parte del Club. <br> Si no lo recibes, no olvides revisar la bandeja de no deseados, Spam, y Promociones",
		];

		wp_send_json( $res );
	}


	public function process_direct_purchase($id_event, $id_user){
		// Direct purchase process
		$direct_purchase     = get_post_meta( $id_event, DCMS_DIRECT_PURCHASE, true );

		if ( ! $direct_purchase ) {
			return;
		}

		// Direct purchase selected user automatically
		$db = new Database();
		$db->update_selected_event_user( $id_event, $id_user);

		$url_page_purchase = DCMS_URL_PAGE_PURCHASE . Helper::set_params_url_purchase( $id_user, $id_event );

		$res = [
			'status'  => 1,
			'joined'  => 1,
			'redirect' => $url_page_purchase,
			'message' => "✅ Te has inscrito correctamente al Evento, <br> Te redireccionaremos a la zona de pago...",
		];

		wp_send_json( $res);
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

		$db     = new Database();
		$result = $db->find_user_identify_pin( $identify, $pin );

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

			// Validate if children have the event assigned
			foreach ( $ids_children as $id_child ) {
				$joined = $db->search_user_in_event( $id_child, $id_post );

				error_log( print_r( "Se unió " . $joined . " - " . $id_child . " - " . $id_post, true ) );

				if ( $joined == 1 ) {
					$res = [
						'status'  => 0,
						'message' => "➜ Uno de los acompañantes ya participa en el evento, seleccione otro o elimine el acompañante"
					];
					echo json_encode( $res );
					wp_die();
				}
			}

			// Save children and user in event
			foreach ( $ids_children as $id_child ) {

				$result = $db->save_children( $id_child, $id_post, $parent, $id_user );
				Helper::validate_add_children( $result );

				//Children data
				$user_data                        = get_user_meta( $id_child );
				$child_name                       = $user_data['name'][0] . ' ' . $user_data['lastname'][0];
				$child_identify                   = $user_data['identify'][0];
				$children_data[ $child_identify ] = $child_name;
			}


			// Update user inscription, assign parent the same id
			$db->save_join_user_to_event( $id_post, $id_user, $parent );

			// Process direct purchase
			$this->process_direct_purchase($id_post, $id_user);

			//Send email user
			( new Mail )->send_email_join_event( $name, $email, $event_title, $event_excerpt, $children_data );
		}


		// If all is ok
		$res = [
			'status'  => 1,
			'message' => "➜ Los acompañantes se agregaron correctamente <br> En unas horas recibirás en tu email la confirmación por parte del Club. <br> Si no lo recibes, no olvides revisar la bandeja de no deseados, Spam, y Promociones"
		];

		wp_send_json( $res);
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

			// get an array [id_variation = qty] (variable product) or [id_product = qty] ( simple product)
			$products_qty = $this->get_qty_variable_products( $id_product, $id_user, $children_set );

			// Empty cart
			$woocommerce->cart->empty_cart();

			try {
				if ( $products_qty ) {
					foreach ( $products_qty as $product_id => $qty ) {
						WC()->cart->add_to_cart( $product_id, $qty );
					}

					// Build redirection to cart
					$res = [
						'status'  => 1,
						'message' => "Redireccionando...",
						'url'     => wc_get_cart_url()
					];
				} else {
					$res = [
						'status'  => 0,
						'addClass' => 'message-joined',
						'message' => 'Estimado/a abonado/a no necesita realizar la compra del suplemento, puede acceder directamente con su carné de abonado/a usted y sus acompañantes inscritos'
					];
				}
			} catch ( \Exception $e ) {
				$res['message'] = "Hubo un error al agregar al carrito - " . $e->getMessage();
			}

		} else {
			$res['message'] = "El usuario conectado no coincide con el usuario del evento";
		}

		wp_send_json( $res );
	}

	public function get_qty_variable_products( $id_product, $id_user, $children_ids ): array {

		// Get users groups
		$ids   = $children_ids;
		$ids[] = $id_user; // At least the main user has to be in the group

		$db   = new Database();

		// Get count users by group
		$groups_user = $db->get_totals_group_user_type( $ids );

		// If it is a variable product, get the variations, or if it is a simple product return []
		$variations_product = $db->get_variations_product( $id_product );

		$results = [];

		// Get the qty of each variation
		if ( ! empty( $variations_product ) ){
			foreach ( $groups_user as $type => $qty ) {
				$type_slug = $this->custom_sanitize_slug( $type );
				if ( array_key_exists( $type_slug, $variations_product ) ) {
					$id_variation             = $variations_product[ $type_slug ];
					$results[ $id_variation ] = $qty;
				}
			}
		}

		// If it is a simple product, return the qty, sum of all users groups
		if ( empty( $results ) && ! empty( $groups_user ) ) {
			$results[ $id_product ] = array_sum( $groups_user );
		}

		return $results;
	}

	// Some groups key have dot "." in it's name , this function replace it with "-2" to match with the slug of the variation
	private function custom_sanitize_slug($type):string{
		return sanitize_title(str_replace('.','-2',$type));
	}

}

