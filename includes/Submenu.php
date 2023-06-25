<?php

namespace dcms\event\includes;

use dcms\event\backend\includes\inscribed\Inscribed;

/**
 * Class for creating a dashboard submenu
 */
class Submenu{
    // Constructor
    public function __construct(){
        add_action('admin_menu', [$this, 'register_submenu'], 100);
    }

    // Register submenu
    public function register_submenu(){
        add_submenu_page(
            DCMS_EVENT_MENU,
            __('Configuración','dcms-events-users'),
            __('Configuración','dcms-events-users'),
            'manage_options',
            'event-settings',
            [$this, 'submenu_page_settings_callback'],
            20
        );
        add_submenu_page(
            DCMS_EVENT_MENU,
            __('Inscritos seleccionados','dcms-events-users'),
            __('Inscritos seleccionados','dcms-events-users'),
            'manage_options',
            'inscribed-selected',
            [$this, 'submenu_page_inscribed_selected_callback'],
            2
        );

        add_submenu_page(
            DCMS_EVENT_MENU,
            __('Envío SEPA','dcms-events-users'),
            __('Envío SEPA','dcms-events-users'),
            'manage_options',
            'send-sepa',
            [$this, 'submenu_page_send_sepa_callback'],
            3
        );

    }

    // Callback, show view
    public function submenu_page_settings_callback(){
        include_once (DCMS_EVENT_PATH. 'backend/views/screen-settings.php');
    }

    // Call back show send sepa view
    public function submenu_page_send_sepa_callback(){
        include_once( DCMS_EVENT_PATH . 'backend/views/sepa/list-send-sepa.php' );
    }

    // Callback, show view
    public function submenu_page_inscribed_selected_callback(){
	    wp_enqueue_style('admin-event-style');

	    wp_enqueue_script('admin-inscribed-selected');
	    wp_localize_script('admin-inscribed-selected','dcms_inscribed_selected',[
		    'ajaxurl'=>admin_url('admin-ajax.php'),
		    'nonce' => wp_create_nonce('ajax-inscribed-selected')
	    ]);

	    wp_enqueue_script('admin-lib-sheet-js'); //library to read xls with javascript

	    (new Inscribed())->get_inscribed_screen();
    }
}