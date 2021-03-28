<?php

namespace dcms\event\backend;

use dcms\event\includes\Database;

// Custom post type class
class Single{

    public function __construct(){
        add_action('edit_form_advanced', [$this, 'add_filter_area'], 10, 1);
        add_action('save_post_'.DCMS_EVENT_CPT, [$this, 'save_list_filter'], 10, 3);
    }

    // Show filter area and users saved for event
    public function add_filter_area( $post ){
        $screen = get_current_screen();

        if( $screen->post_type == DCMS_EVENT_CPT ) {

            wp_enqueue_style('admin-event-style');
            wp_enqueue_script('admin-event-script');
            wp_localize_script('admin-event-script','dcms_vars',['ajaxurl'=>admin_url('admin-ajax.php')]);

            $db = new Database();
            $items = $db->select_user_event();

            include_once ('views/single-list-filter.php');
        }
    }

    // Save filter list
    public function save_list_filter( $post_id, $post, $update ){
        $ids_user = [];

        if ( isset($_POST['id_user_event']) && $_POST['id_user_event'] != ''  ){
            $ids_user = explode( ',', $_POST['id_user_event'] );
        }

        if ( is_array($ids_user) && count($ids_user) > 0 ){
            $db = new Database();

            $db->remove_users_event($post_id);
            $res = $db->save_users_event($ids_user, $post_id);

            if ( ! $res ) error_log( 'Error to insert users in event' );
        }

    }
}

