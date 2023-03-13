<?php

namespace dcms\event\includes;

// Custom post type class
class Enqueue {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts_backend' ] );
	}

	// Front-end script
	public function register_scripts() {
		wp_register_script( 'event-script',
			DCMS_EVENT_URL . '/assets/script.js',
			[ 'jquery' ],
			DCMS_EVENT_VERSION,
			true );

		wp_register_style( 'event-style',
			DCMS_EVENT_URL . '/assets/style.css',
			[],
			DCMS_EVENT_VERSION );
	}

	// Backend scripts
	public function register_scripts_backend() {
		// Library to read xls with javascript
		wp_register_script( 'admin-lib-sheet-js',
			'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.min.js',
			[],
			DCMS_EVENT_VERSION,
			true );

		wp_register_script( 'admin-single-event',
			DCMS_EVENT_URL . '/backend/assets/single-event.js',
			[ 'jquery' ],
			DCMS_EVENT_VERSION,
			true );

		wp_register_script( 'admin-inscribed-selected',
			DCMS_EVENT_URL . '/backend/assets/inscribed-selected.js',
			[ 'jquery', 'admin-lib-sheet-js' ],
			DCMS_EVENT_VERSION,
			true );


		wp_register_style( 'admin-event-style',
			DCMS_EVENT_URL . '/backend/assets/style.css',
			[],
			DCMS_EVENT_VERSION );

	}

}