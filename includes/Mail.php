<?php

namespace dcms\event\includes;

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

}
