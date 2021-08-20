<?php

namespace dcms\event\includes;

// Custom post type class
class Enqueue{

    public function __construct(){
        add_action('wp_enqueue_scripts', [$this, 'register_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'register_scripts_backend']);
    }

    // Front-end script
    public function register_scripts(){
        wp_register_script('event-script',
                DCMS_EVENT_URL.'/assets/script.js',
                ['jquery'],
                DCMS_EVENT_VERSION,
                true);

        wp_register_style('event-style',
                DCMS_EVENT_URL.'/assets/style.css',
                [],
                DCMS_EVENT_VERSION );
    }

    // Backend scripts
    public function register_scripts_backend(){

        wp_register_script('admin-event-script',
                            DCMS_EVENT_URL.'/backend/assets/event-script.js',
                            ['jquery'],
                            DCMS_EVENT_VERSION,
                            true);

        wp_register_script('admin-report-script',
                            DCMS_EVENT_URL.'/backend/assets/report-script.js',
                            ['jquery'],
                            DCMS_EVENT_VERSION,
                            true);


        wp_register_style('admin-event-style',
                            DCMS_EVENT_URL.'/backend/assets/style.css',
                            [],
                            DCMS_EVENT_VERSION );

    }

}