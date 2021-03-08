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
            DCMS_SUBMENU,
            __('User Events','dcms-events-users'),
            __('User Events','dcms-events-users'),
            'manage_options',
            'events-users',
            [$this, 'submenu_page_callback']
        );
    }

    // Callback, show view
    public function submenu_page_callback(){
        include_once (DCMS_EVENT_PATH. '/views/main-screen.php');
    }
}