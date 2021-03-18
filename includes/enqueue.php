<?php

namespace dcms\event\includes;

// Custom post type class
class Enqueue{

    public function __construct(){
        add_action('admin_enqueue_scripts', [$this, 'register_scripts']);
    }

    public function register_scripts(){

        wp_register_script('admin-event-script',
                            DCMS_EVENT_URL.'/backend/assets/script.js',
                            ['jquery'],
                            DCMS_EVENT_VERSION,
                            true);

        wp_register_style('admin-event-style',
                            DCMS_EVENT_URL.'/backend/assets/style.css',
                            [],
                            DCMS_EVENT_VERSION );

    }

}