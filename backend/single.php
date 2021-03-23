<?php

namespace dcms\event\backend;

// Custom post type class
class Single{

    public function __construct(){
        add_action('edit_form_advanced', [$this, 'add_filter_area'], 10, 1);
        add_action('save_post_'.DCMS_EVENT_CPT, [$this, 'save_list_filter'], 10, 3);
    }

    // Show filter area
    public function add_filter_area( $post ){
        $screen = get_current_screen();

        if( $screen->post_type == DCMS_EVENT_CPT ) {

            wp_enqueue_style('admin-event-style');
            wp_enqueue_script('admin-event-script');
            wp_enqueue_script('admin-event-modal');

            wp_localize_script('admin-event-script','dcms_vars',['ajaxurl'=>admin_url('admin-ajax.php')]);

            include_once ('views/single-list-filter.php');
        }
    }

    // Save filter list
    public function save_list_filter( $post_id, $post, $update ){

    }
}

