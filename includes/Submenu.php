<?php

namespace dcms\event\includes;

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
            __('Inscritos evento','dcms-events-users'),
            __('Inscritos evento','dcms-events-users'),
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
        include_once (DCMS_EVENT_PATH. 'backend/views/screen-inscribed.php');
    }
}