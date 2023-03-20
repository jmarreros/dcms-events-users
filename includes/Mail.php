<?php

namespace dcms\event\includes;

use dcms\event\helpers\Helper;

class Mail {

	private array $options;

	public function __construct() {
		$this->options = get_option( 'dcms_events_options' );

		add_filter( 'wp_mail_from', [ $this, 'change_sender_email' ] );
		add_filter( 'wp_mail_from_name', [ $this, 'change_sender_name' ] );
	}

	// Change sender email
	public function change_sender_email() {
		return $this->options['dcms_sender_email'];
	}

	// Change sender name
	public function change_sender_name() {
		return $this->options['dcms_sender_name'];
	}

	// Generic email send with template and parameters
	public function send_mail_template( $template_name, $user, $event ): bool {
		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

		$subject = $this->options[ 'dcms_subject_email_' . $template_name ];
		$body    = $this->options[ 'dcms_text_email_' . $template_name ];

		$user_id           = $user['id']??0;
		$user_name         = $user['name'];
		$user_email        = $user['email'];
		$user_convivientes = $user['convivientes'];

		$event_id      = $event['id']??0;
		$event_title   = $event['title'];
		$event_excerpt = $event['excerpt'];

		if ( $user_id && $event_id){
			$params = "?idu=$user_id&ide=$event_id&idn=" .
			          urlencode_deep( Helper::set_sporting_nonce( $user_id, $event_id ) );
		}

		$body = str_replace( '%name%', $user_name, $body );
		$body = str_replace( '%event_title%', $event_title, $body );
		$body = str_replace( '%event_extracto%', $event_excerpt, $body );
		$body = str_replace( '%params_integration%', $params, $body );

		$str = '';
		if ( count( $user_convivientes ) > 0 ) {
			$str = "Convivientes: <br>";
			$str .= "<ul>";
			foreach ( $user_convivientes as $key => $value ) {
				$str .= "<li> ID: " . $key . " - " . $value . "</li>";
			}
			$str .= "</ul>";
		}
		$body = str_replace( '%convivientes%', $str, $body );

		return wp_mail( $user_email, $subject, $body, $headers );
	}
}
