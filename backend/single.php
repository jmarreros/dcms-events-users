<?php

namespace dcms\event\backend;

// Custom post type class
class Single{

    public function __construct(){
        add_action('edit_form_advanced', [$this, 'add_filter_area']);

    }

    public function add_filter_area(){
        $screen = get_current_screen();

        if( $screen->post_type=='events_sporting' ) {

            wp_enqueue_style('admin-event-style');
            wp_enqueue_script('admin-event-script');

            echo "🚀 Aqui nueva área!";
        }
    }

}

