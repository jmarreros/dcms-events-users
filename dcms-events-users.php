<?php
/*
Plugin Name: Sporting Event users
Plugin URI: https://webservi.es
Description: Integrates events users
Version: 1.5
Author: Webservi.es
Author URI: https://decodecms.com
Text Domain: dcms-events-users
Domain Path: languages
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

namespace dcms\event;

require __DIR__ . '/vendor/autoload.php';

use dcms\event\includes\Cpt;
use dcms\event\includes\Plugin;
use dcms\event\includes\Submenu;
use dcms\event\includes\Enqueue;
use dcms\event\includes\Shortcode;
use dcms\event\includes\Account;
use dcms\event\includes\Event;
use dcms\event\includes\Settings;
use dcms\event\includes\Export;
use dcms\event\backend\single\Single;
use dcms\event\backend\single\Metabox;
use dcms\event\backend\single\Filter;
use dcms\event\backend\inscribed\Inscribed;
use dcms\event\backend\inscribed\Selected;
use dcms\event\includes\FormSepa;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin class to handle settings constants and loading files
 **/
final class Loader {

	// Define all the constants we need
	public function define_constants() {
		define( 'DCMS_EVENT_VERSION', '1.6.2' );
		define( 'DCMS_EVENT_PATH', plugin_dir_path( __FILE__ ) );
		define( 'DCMS_EVENT_URL', plugin_dir_url( __FILE__ ) );
		define( 'DCMS_EVENT_BASE_NAME', plugin_basename( __FILE__ ) );
		define( 'DCMS_EVENT_CPT', 'events_sporting' );
		define( 'DCMS_EVENT_MENU', 'edit.php?post_type=' . DCMS_EVENT_CPT );
		define( 'DCMS_EVENT_COUNT_META', 'dcms_count_event' ); //count assign event to user, user meta data

		// Shortcodes
		define( 'DCMS_EVENT_ACCOUNT', 'sporting-user-account' );
		define( 'DCMS_EVENT_SIDEBAR', 'sporting-sidebar-user' );
		define( 'DCMS_EVENT_LIST', 'sporting-event-list' );
		define( 'DCMS_SET_PURCHASE', 'establecer-compra-evento' );
		define( 'DCMS_SET_FORM_SEPA', 'formulario-enviar-sepa' );

		// Convivientes
		define( 'DCMS_ENABLE_CONVIVIENTES', 'event-enable-convivientes' );
		define( 'DCMS_LOCK_INSCRIPTIONS', 'event-lock-inscriptions' );
		define( 'DCMS_MAX_CHILDREN', 1 );

		// Event
		define( 'DCMS_EVENT_PRODUCT_ID', 'event-product-id' );

		// Sepa PDF Path
		define( 'DCMS_SEPA_FILES_PATH', WP_CONTENT_DIR . '/uploads/SEPA-files/' );
		define( 'DCMS_SEPA_FILES_URL', content_url() . '/uploads/SEPA-files/' );
	}

	// Load tex domain
	public function load_domain() {
		add_action( 'plugins_loaded', function () {
			$path_languages = dirname( DCMS_EVENT_BASE_NAME ) . '/languages/';
			load_plugin_textdomain( 'dcms-events-users', false, $path_languages );
		} );
	}

	// Add link to plugin list
	public function add_link_plugin() {
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
			return array_merge( array(
				'<a href="' . esc_url( admin_url( DCMS_EVENT_MENU ) ) . '">' . __( 'Settings', 'dcms-events-users' ) . '</a>'
			), $links );
		} );
	}

	// Initialize all
	public function init() {
		$this->define_constants();
		$this->load_domain();
		$this->add_link_plugin();
		new Cpt();
		new Plugin();
		new Submenu();
		new Enqueue();
		new Shortcode();
		new Account();
		new Event();
		new Settings();
		new Single();
		new Metabox();
		new Filter();
		new Export();
		new Selected();
		new Inscribed();
		(new FormSepa())->init();
	}
}

$dcms_event_process = new Loader();
$dcms_event_process->init();
