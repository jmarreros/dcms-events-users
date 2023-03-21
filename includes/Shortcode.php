<?php

namespace dcms\event\includes;

use dcms\event\backend\inscribed\Selected;
use dcms\event\helpers\Helper;

// Class for grouping shortcodes functionality
class Shortcode {

	public function __construct() {
		add_action( 'init', [ $this, 'create_shortcodes' ] );
	}

	// Function to add shortcodes
	public function create_shortcodes() {
		global $wp;

		add_shortcode( DCMS_EVENT_ACCOUNT, [ $this, 'show_user_account' ] );
		add_shortcode( DCMS_EVENT_SIDEBAR, [ $this, 'show_user_sidebar' ] );
		add_shortcode( DCMS_EVENT_LIST, [ $this, 'show_list_events' ] );
		add_shortcode( DCMS_SET_PURCHASE, [ $this, 'show_set_purchase' ] );

		// For link mail
		$wp->add_query_var( 'idu' ); //id user
		$wp->add_query_var( 'ide' ); //id event
		$wp->add_query_var( 'idn' ); //id nonce
	}

	// Function show user account in the front-end
	public function show_user_account(): string {
		$id_user = get_current_user_id();

		if ( $id_user ) {
			$db = new Database();

			wp_enqueue_style( 'event-style' );
			wp_enqueue_script( 'event-script' );

			wp_localize_script( 'event-script',
				'dcms_uaccount',
				[
					'ajaxurl'  => admin_url( 'admin-ajax.php' ),
					'naccount' => wp_create_nonce( 'ajax-nonce-account' )
				] );

			$data            = $db->show_user_details( $id_user );
			$text_fields     = Helper::get_account_fields();
			$editable_fields = Helper::get_editable_fields();

			ob_start();
			include_once DCMS_EVENT_PATH . 'views/details-users-account.php';
			$html_code = ob_get_contents();
			ob_end_clean();

			return $html_code;
		}

		return '';
	}

	// Function to show user sidebar
	public function show_user_sidebar( $atts, $content ): string {
		$id_user = get_current_user_id();

		if ( $id_user ) {
			$db = new Database();

			wp_enqueue_style( 'event-style' );
			wp_enqueue_script( 'event-script' );

			$user = $db->show_user_sidebar( $id_user );

			if ( $user ):
				$email  = Helper::search_field_in_meta( $user, 'email' );
				$name   = Helper::search_field_in_meta( $user, 'name' ) . ' ' . Helper::search_field_in_meta( $user, 'lastname' );
				$number = Helper::search_field_in_meta( $user, 'number' );
			endif;

			$content = $content ?? '';
			$email   = $email ?? '';
			$name    = $name ?? '';
			$number  = $number ?? '';

			ob_start();
			include_once DCMS_EVENT_PATH . 'views/user-sidebar.php';
			$html_code = ob_get_contents();
			ob_end_clean();

			return $html_code;
		}

		return '';
	}

	// Function to show liste events in the front-end
	public function show_list_events(): string {
		$id_user = get_current_user_id();

		if ( $id_user ) {
			$db = new Database();

			wp_enqueue_style( 'event-style' );
			wp_enqueue_script( 'event-script' );

			// Ajax event
			wp_localize_script( 'event-script',
				'dcms_uevents',
				[
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'join'    => __( 'Unirse al Evento', 'dcms-events-users' ),
					'nojoin'  => __( 'Inscrito al Evento', 'dcms-events-users' ),
					'nevent'  => wp_create_nonce( 'ajax-nonce-event' )
				] );

			// Ajax children
			wp_localize_script( 'event-script',
				'dcms_echildren',
				[
					'ajaxurl'   => admin_url( 'admin-ajax.php' ),
					'nchildren' => wp_create_nonce( 'ajax-nonce-event-children' )
				] );


			$events = $db->get_events_for_user( $id_user );

			ob_start();
			include_once DCMS_EVENT_PATH . 'views/list-events-user.php';
			$html_code = ob_get_contents();
			ob_end_clean();

			return $html_code;
		}

		return '';
	}

	// To show form setting purchase
	public function show_set_purchase() {
		// Validate user login
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$id_user  = get_query_var( 'idu', 0 );
		$id_event = get_query_var( 'ide', 0 );
		$id_nonce = get_query_var( 'idn', 0 );

		// Verify user id
		// TODO descomentar esto
//		if ( get_current_user_id() != $id_user ) {
//			return 'Not valid user for link';
//		}

		// Verify link
		if ( ! Helper::validate_sporting_nonce( $id_user, $id_event, $id_nonce ) ) {
			return 'Not valid nonce';
		}

		$id_product = get_post_meta( $id_event, DCMS_EVENT_PRODUCT_ID );
		if ( ! $id_product ) {
			return 'No hay un producto asociado a este evento - consulta con el administrador del sitio';
		}

		wp_enqueue_style( 'event-style' );
		wp_enqueue_script( 'event-script' );

		// Ajax event
		wp_localize_script( 'event-script',
			'dcms_uevents',
			[
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nevent'  => wp_create_nonce( 'ajax-nonce-event' )
			] );

		$data_event = ( new Selected() )->data_selected_user_event( $id_user, $id_event );

		if ( $data_event && ! $data_event['id_order'] ) {

			if ( $data_event['children'] > 0 ) {
				$user_name  = ( get_userdata( $id_user ) )->display_name;
				$event_name = get_the_title( $id_event );
				$children   = ( new User() )->get_arr_children_user( $id_user, $id_event );

				ob_start();
				include_once DCMS_EVENT_PATH . 'views/set-purchase.php';
				$html_code = ob_get_contents();
				ob_end_clean();
			} else { // redirect
				wp_redirect( wc_get_cart_url() . "?add-to-cart=$id_product&quantity=1" );
			}

		} else {
			$html_code = "El enlace esta expirado o el evento ya ha sido pagado";
		}

		return $html_code;
	}


}