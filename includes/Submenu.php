<?php

namespace dcms\event\includes;

use dcms\event\includes\Database;
use dcms\event\helpers\Helper;

/**
 * Class for creating a dashboard submenu
 */
class Submenu{
    // Constructor
    public function __construct(){
        add_action('admin_menu', [$this, 'register_submenu']);
    }

    // Register submenu
    public function register_submenu(){
        add_submenu_page(
            DCMS_EVENT_MENU,
            __('Event Settings','dcms-events-users'),
            __('Event Settings','dcms-events-users'),
            'manage_options',
            'event-settings',
            [$this, 'submenu_page_settings_callback']
        );
        add_submenu_page(
            DCMS_EVENT_MENU,
            __('Inscritos por evento','dcms-events-users'),
            __('Inscritos por evento','dcms-events-users'),
            'manage_options',
            'inscribed-event',
            [$this, 'submenu_page_inscribed_callback']
        );
    }

    // Callback, show view
    public function submenu_page_settings_callback(){
        include_once (DCMS_EVENT_PATH. 'backend/views/screen-settings.php');
    }

    // Callback, show view
    public function submenu_page_inscribed_callback(){

        wp_enqueue_style('admin-event-style');

        wp_enqueue_script('admin-report-script');
        wp_localize_script('admin-report-script','dcms_report_event',[
            'ajaxurl'=>admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-report-event')
        ]);

        $db = new Database();

        $id_event = $_POST['select_event']??0;
        $aviable_events = $db->get_avaiable_events();

        if ( !$id_event && count($aviable_events)){
            $id_event  = $aviable_events[0]->ID; // get the recent aviable event
        }

        if ( $id_event ){
            $suscribed_users = $db->select_users_event_export($id_event, true);
        }

        $fields_table    = Helper::get_inscribed_user_fields();
        include_once (DCMS_EVENT_PATH. 'backend/views/screen-inscribed.php');
    }
}